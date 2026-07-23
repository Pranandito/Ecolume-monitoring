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
    private string $bucketAllTime;

    public function __construct()
    {
        $this->bucket        = env('INFLUXDB_BUCKET');
        // Bucket downsample tanpa retention pendek, dipakai sebagai fallback
        // ketika data di $this->bucket sudah kadaluarsa (retention 30 hari)
        $this->bucketAllTime = env('INFLUXDB_BUCKET_ALLTIME', 'PTSP_AllTime');
    }

    /**
     * Parse dan validasi payload JSON dari MQTT.
     *
     * @param  string $jsonPayload  Raw string JSON dari broker MQTT
     * @return array                Data yang sudah divalidasi
     * @throws \InvalidArgumentException  Jika JSON tidak valid atau field wajib kurang
     */
    public function ParseAndValidate(string $jsonPayload): array
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
     * Ambil nilai kumulatif terakhir (Energi, Volume, Durasi_Operasional) dari sebuah
     * bucket InfluxDB tertentu. Dipakai baik untuk bucket utama (retention pendek)
     * maupun bucket fallback (PTSP_AllTime) yang punya nama field berbeda.
     *
     * @param  string $bucket       Nama bucket yang di-query
     * @param  string $id           Device_Id (tag) yang dicari
     * @param  string $fieldEnergi  Nama field Energi pada bucket ini
     * @param  string $fieldVolume  Nama field Volume pada bucket ini
     * @param  string $fieldDurasi  Nama field Durasi_Operasional pada bucket ini
     * @param  string $range        Rentang waktu query (Flux duration), default -30d
     * @return array{found: bool, energi: float, volume: float, durasi: int, ts: ?int}
     */
    private function getLastCumulative(
        string $bucket,
        string $id,
        string $fieldEnergi,
        string $fieldVolume,
        string $fieldDurasi,
        string $range = '-30d'
    ): array {
        $result = [
            'found'  => false,
            'energi' => 0.0,
            'volume' => 0.0,
            'durasi' => 0,
            'ts'     => null,
        ];

        try {
            $queryApi = InfluxDB::createQueryApi();

            $fluxQuery = <<<FLUX
            from(bucket: "{$bucket}")
            |> range(start: {$range})
            |> filter(fn: (r) => r._measurement == "IoT_PTSP")
            |> filter(fn: (r) => r.Device_Id == "{$id}")
            |> filter(fn: (r) => r._field == "{$fieldEnergi}" or r._field == "{$fieldVolume}" or r._field == "{$fieldDurasi}")
            |> last()
            FLUX;

            $tables = $queryApi->query($fluxQuery);

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $field = $record->getField();
                    $value = $record->getValue();

                    if ($field === $fieldEnergi) {
                        $result['energi'] = (float) $value;
                        $result['found']  = true;

                        // Parse timestamp secara defensif
                        $timeValue = $record->getTime();
                        if ($timeValue instanceof \DateTimeInterface) {
                            $result['ts'] = $timeValue->getTimestamp();
                        } elseif (is_string($timeValue) && $timeValue !== '') {
                            try {
                                $result['ts'] = (new \DateTime($timeValue))->getTimestamp();
                            } catch (\Exception) {
                                $result['ts'] = null;
                            }
                        }
                    }
                    if ($field === $fieldVolume) {
                        $result['volume'] = (float) $value;
                        $result['found']  = true;
                    }
                    if ($field === $fieldDurasi) {
                        $result['durasi'] = (int) $value;
                        $result['found']  = true;
                    }
                }
            }
        } catch (\Exception $e) {
            // Query gagal → kembalikan hasil default (found = false)
        }

        return $result;
    }

    /**
     * Simpan data pompa dari payload MQTT ke InfluxDB.
     *
     * @param  string $jsonPayload  Raw string JSON dari broker MQTT
     * @return bool                 true jika berhasil, false jika gagal tulis ke InfluxDB
     * @throws \InvalidArgumentException  Jika payload tidak valid
     */
    public function MQTTStore(string $jsonPayload, string $serial_number, $id)
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

        // 2a. Coba ambil dari bucket utama (retention 30 hari)
        $last = $this->getLastCumulative(
            $this->bucket,
            $id,
            'Energi',
            'Volume',
            'Durasi_Operasional'
        );

        // 2b. Kalau tidak ketemu (mis. karena retention 30 hari sudah menghapus
        //     data terakhir device ini), fallback ke bucket PTSP_AllTime.
        //     Nama field di bucket ini sedikit berbeda (pakai suffix _delta)
        //     walau isinya tetap nilai kumulatif terakhir.
        if (!$last['found']) {
            $last = $this->getLastCumulative(
                $this->bucketAllTime,
                $id,
                'Energi_delta',
                'Volume_delta',
                'Durasi_Operasional_delta',
                '-3650d' // bucket AllTime tidak dibatasi retention pendek
            );
        }

        if ($last['found']) {
            $prevEnergi = $last['energi'];
            $prevVolume = $last['volume'];
            $prevDurasi = $last['durasi'];
            $prevTs     = $last['ts'];
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
        $point = Point::measurement('IoT_PTSP')
            // Tags
            ->addTag('Device_Id', $id)
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
            // ->addField('Interval_S',         $intervalS)
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

            return [true, "{$this->bucket}/IoT_PTSP/{$id}"];
        } catch (\Exception $e) {
            return [false . ""];
        }
    }
}
