<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfluxDB2\Point;
use InfluxDB2\WriteType;
use PhpMqtt\Client\Facades\MQTT;
use RikoDEV\InfluxDB\Facades\InfluxDB;

class MQTTController extends Controller
{
    public function publish(Request $request)
    {
        $data = [
            'device_id' => $request->device_id,
            'suhu'      => $request->suhu,
            'timestamp' => now()->toIso8601String(),
        ];

        MQTT::publish('test/topic', json_encode($data), 1);

        return response()->json(['status' => 'published']);
    }

    private string $bucket;

    public function __construct()
    {
        $this->bucket = env('INFLUXDB_BUCKET');
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Validasi input — energi & volume dihitung server, tidak dari request
        $validated = $request->validate([
            'device_id'          => 'required|string|max:50',
            'tegangan'           => 'required|numeric',   // Volt
            'daya'               => 'required|numeric',   // Watt
            'debit'              => 'required|numeric',   // liter/s
            'suhu'               => 'required|numeric',   // Celsius
            'timestamp'          => 'nullable|integer',   // Unix timestamp (opsional)
        ]);

        $deviceId  = $validated['device_id'];
        $nowTs     = isset($validated['timestamp']) ? (int) $validated['timestamp'] : time();
        $daya      = (float) $validated['daya'];
        $debit     = (float) $validated['debit'];

        // ----------------------------------------------------------------
        // 2. Ambil data point terakhir dari InfluxDB untuk device ini
        // ----------------------------------------------------------------
        $DEFAULT_INTERVAL_S = 120;   // fallback: 2 menit (interval kirim IoT)
        $MAX_INTERVAL_S     = 300;   // batas wajar: 5 menit

        $prevEnergi   = 0.0;
        $prevVolume   = 0.0;
        $prevDurasi   = 0;
        $prevTs       = null;

        try {
            $queryApi = InfluxDB::createQueryApi();

            $fluxQuery = <<<FLUX
        from(bucket: "{$this->bucket}")
          |> range(start: -7d)
          |> filter(fn: (r) => r._measurement == "Data_Pompa")
          |> filter(fn: (r) => r.Device_id == "{$deviceId}")
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
        $totalEnergi  = $prevEnergi + $deltaEnergi;
        $totalVolume  = $prevVolume + $deltaVolume;
        $totalDurasi  = $prevDurasi + $intervalS;

        // ----------------------------------------------------------------
        // 6. Buat Point InfluxDB
        // ----------------------------------------------------------------
        $point = Point::measurement('Data_Pompa')
            // Tags
            ->addTag('Device_id', $deviceId)
            // Fields - float
            ->addField('Tegangan',          (float) $validated['tegangan'])
            ->addField('Daya',              $daya)
            ->addField('Debit',             $debit)
            ->addField('Suhu',              (float) $validated['suhu'])
            ->addField('Energi',            round($totalEnergi, 4))
            ->addField('Volume',            round($totalVolume, 4))
            // Fields - integer
            ->addField('Durasi_Operasional', $totalDurasi)
            // Metadata tambahan (opsional, berguna untuk debug)
            ->addField('Interval_S',        $intervalS)
            // Timestamp (presisi detik)
            ->time($nowTs);

        // ----------------------------------------------------------------
        // 7. Tulis ke InfluxDB
        // ----------------------------------------------------------------
        try {
            $writeApi = InfluxDB::createWriteApi([
                'writeType' => WriteType::SYNCHRONOUS,
            ]);

            $writeApi->write($point);
            InfluxDB::close();

            return response()->json([
                'success'    => true,
                'message'    => 'Data pompa berhasil disimpan.',
                'device_id'  => $deviceId,
                'timestamp'  => $nowTs,
                'interval_s' => $intervalS,
                'delta'      => [
                    'energi_wh' => round($deltaEnergi, 4),
                    'volume_l'  => round($deltaVolume, 4),
                ],
                'kumulatif'  => [
                    'energi_wh'          => round($totalEnergi, 4),
                    'volume_l'           => round($totalVolume, 4),
                    'durasi_operasional' => $totalDurasi,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data ke InfluxDB.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
