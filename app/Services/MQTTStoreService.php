<?php

namespace App\Services;

use App\Models\DeviceConfig;
use Illuminate\Support\Facades\Http;
use InfluxDB2\Point;
use InfluxDB2\WriteType;
use RikoDEV\InfluxDB\Facades\InfluxDB;

class MQTTStoreService
{

    function getLocation($lat, $lng)
    {
        // Parameter zoom=14 biasanya pas untuk tingkat kecamatan/kota
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=14&addressdetails=1";

        // Nominatim mewajibkan header User-Agent yang valid
        $response = Http::withoutVerifying()->withHeaders([
            'User-Agent' => 'Ecolume-PTSP/1.0 (ditopranandito@gmail.com)'
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['address'])) {
                $address = $data['address'];

                // Backup bertingkat untuk Kota/Kabupaten
                $kotaKabupaten = $address['city']
                    ?? $address['town']
                    ?? $address['county']
                    ?? $address['municipality']
                    ?? $address['state_district']
                    ?? 'Tidak Diketahui';

                $kotaKabupaten = trim(str_ireplace('Kabupaten', '', $kotaKabupaten));

                // Backup bertingkat untuk Kecamatan/Kelurahan/Desa
                $kecamatan = $address['village']
                    ?? $address['suburb']
                    ?? $address['neighbourhood']
                    ?? $address['city_district']
                    ?? 'Tidak Diketahui';

                return [
                    'kota' => $kotaKabupaten,
                    'kecamatan' => $kecamatan
                ];
            } else {
                return [
                    'kota' => "",
                    'kecamatan' => ""
                ];
            }
        }

        // Response backup jika API OpenStreetMap gagal dihubungi atau data tidak sesuai
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data lokasi atau format alamat tidak dikenali'
        ], 500);
    }

    private string $bucket;

    public function __construct()
    {
        $this->bucket = env('INFLUXDB_BUCKET');
    }

    /**
     * Parse dan validasi payload JSON dari MQTT.
     *
     * @param  string $jsonPayload  Raw string JSON dari broker MQTT
     * @return array                Data yang sudah divalidasi
     * @throws \InvalidArgumentException  Jika JSON tidak valid atau field wajib kurang
     */
    private function parseAndValidate(string $jsonPayload): array
    {
        // 1. Decode JSON
        $data = json_decode($jsonPayload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(
                'Payload MQTT bukan JSON valid: ' . json_last_error_msg()
            );
        }

        if (!is_array($data)) {
            throw new \InvalidArgumentException(
                'Payload MQTT harus berupa JSON object.'
            );
        }

        // 2. Definisi field yang wajib ada beserta tipe-nya
        $requiredFields = [
            'tegangan'  => 'numeric',   // Volt
            'daya'      => 'numeric',   // Watt
            'debit'     => 'numeric',   // liter/s
            'suhu'      => 'numeric',   // Celsius
            'latitude'  => 'numeric',   // Derajat desimal, misal: -7.558
            'longitude' => 'numeric',   // Derajat desimal, misal: 110.856
        ];

        $errors = [];

        foreach ($requiredFields as $field => $type) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $errors[] = "Field '{$field}' wajib ada dan tidak boleh kosong.";
                continue;
            }

            if ($type === 'numeric' && !is_numeric($data[$field])) {
                $errors[] = "Field '{$field}' harus berupa angka.";
            }

            if ($type === 'string' && !is_string($data[$field])) {
                $errors[] = "Field '{$field}' harus berupa string.";
            }

            if ($field === 'device_id' && strlen((string) $data[$field]) > 50) {
                $errors[] = "Field 'device_id' maksimal 50 karakter.";
            }
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(
                'Validasi payload MQTT gagal: ' . implode(' | ', $errors)
            );
        }

        return [
            'tegangan'  => (float)   $data['tegangan'],
            'daya'      => (float)   $data['daya'],
            'debit'     => (float)   $data['debit'],
            'suhu'      => (float)   $data['suhu'],
            'latitude'  => round((float) $data['latitude'],  7),
            'longitude' => round((float) $data['longitude'], 7),
        ];
    }

    /**
     * Simpan data pompa dari payload MQTT ke InfluxDB.
     *
     * @param  string $jsonPayload  Raw string JSON dari broker MQTT
     * @return bool                 true jika berhasil, false jika gagal tulis ke InfluxDB
     * @throws \InvalidArgumentException  Jika payload tidak valid
     */
    public function MQTTStore(string $jsonPayload, string $serial_number, $id): bool
    {
        // 1. Parse & validasi JSON payload
        $validated = $this->parseAndValidate($jsonPayload);

        $nowTs     = time();
        $daya      = $validated['daya'];
        $debit     = $validated['debit'];

        // ----------------------------------------------------------------
        // 2. Ambil data point terakhir dari InfluxDB untuk device ini
        // ----------------------------------------------------------------
        $DEFAULT_INTERVAL_S = 120;   // fallback: 2 menit (interval kirim IoT)
        $MAX_INTERVAL_S     = 300;   // batas wajar: 5 menit

        $prevEnergi = 0.0;
        $prevVolume = 0.0;
        $prevDurasi = 0;
        $prevTs     = null;

        try {
            $queryApi = InfluxDB::createQueryApi();

            $fluxQuery = <<<FLUX
            from(bucket: "{$this->bucket}")
            |> range(start: -7d)
            |> filter(fn: (r) => r._measurement == "Data_Pompa")
            |> filter(fn: (r) => r.Device_id == "{$serial_number}")
            |> filter(fn: (r) => r._field == "Energi" or r._field == "Volume" or r._field == "Durasi_Operasional")
            |> last()
            FLUX;

            $tables = $queryApi->query($fluxQuery);

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $field = $record->getField();
                    $value = $record->getValue();

                    if ($field === 'Energi') {
                        $prevEnergi = (float) $value;

                        // Parse timestamp secara defensif
                        $timeValue = $record->getTime();
                        if ($timeValue instanceof \DateTimeInterface) {
                            $prevTs = $timeValue->getTimestamp();
                        } elseif (is_string($timeValue) && $timeValue !== '') {
                            try {
                                $prevTs = (new \DateTime($timeValue))->getTimestamp();
                            } catch (\Exception) {
                                $prevTs = null;
                            }
                        }
                    }
                    if ($field === 'Volume') {
                        $prevVolume = (float) $value;
                    }
                    if ($field === 'Durasi_Operasional') {
                        $prevDurasi = (int) $value;
                    }
                }
            }
        } catch (\Exception $e) {
            // Query gagal atau belum ada data → lanjut dengan nilai default
        }

        // ----------------------------------------------------------------
        // 3. Tentukan interval waktu (detik)
        // ----------------------------------------------------------------
        if ($prevTs !== null) {
            $rawInterval = $nowTs - $prevTs;

            // Jika interval lebih dari 5 menit (anomali/restart),
            // gunakan interval default pengiriman IoT (2 menit)
            $intervalS = ($rawInterval > 0 && $rawInterval <= $MAX_INTERVAL_S)
                ? $rawInterval
                : $DEFAULT_INTERVAL_S;
        } else {
            // Belum ada data sebelumnya — ini data pertama
            $intervalS = $DEFAULT_INTERVAL_S;
        }

        // ----------------------------------------------------------------
        // 4. Hitung delta energi dan volume dari interval ini
        //    Energi (Wh) = Daya (W) × waktu (jam)
        //    Volume (L)  = Debit (L/s) × waktu (s)
        // ----------------------------------------------------------------
        $deltaEnergi = $daya  * ($intervalS / 3600.0); // Wh
        $deltaVolume = $debit * $intervalS;             // liter

        // ----------------------------------------------------------------
        // 5. Nilai kumulatif (terus ditambahkan)
        // ----------------------------------------------------------------
        $totalEnergi = $prevEnergi + $deltaEnergi;
        $totalVolume = $prevVolume + $deltaVolume;
        $totalDurasi = $prevDurasi + ($daya > 0 ? $intervalS : 0);

        // ----------------------------------------------------------------
        // 6. Buat Point InfluxDB
        // ----------------------------------------------------------------
        $point = Point::measurement('Data_Pompa')
            // Tags
            ->addTag('Device_id', $serial_number)
            // Fields - float
            ->addField('Tegangan',           $validated['tegangan'])
            ->addField('Daya',               $daya)
            ->addField('Debit',              $debit)
            ->addField('Suhu',               $validated['suhu'])
            ->addField('Energi',             round($totalEnergi, 4))
            ->addField('Volume',             round($totalVolume, 4))
            // Fields - integer
            ->addField('Durasi_Operasional', $totalDurasi)
            // Metadata tambahan (opsional, berguna untuk debug)
            ->addField('Interval_S',         $intervalS)
            // Timestamp (presisi detik)
            ->time($nowTs);


        // ----------------------------------------------------------------
        // 7. Simpan Data InfluxDB & MySql
        // ----------------------------------------------------------------

        $loc = $this->getLocation($validated['latitude'], $validated['longitude']);

        $locUpdate = DeviceConfig::where('id', $id)->update([
            "lat" => $validated['latitude'],
            "long" => $validated['longitude'],
            "location" => $loc['kota'] . ", " . $loc['kecamatan']
        ]);

        try {
            $writeApi = InfluxDB::createWriteApi([
                'writeType' => WriteType::SYNCHRONOUS,
            ]);

            $writeApi->write($point);
            // InfluxDB::close();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
