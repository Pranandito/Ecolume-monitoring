<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use RikoDEV\InfluxDB\Facades\InfluxDB;

class DashboardController extends Controller
{
    function getLatestData($device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
        |> range(start: 0)
        |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
        |> filter(fn: (r) => r["Device_Id"] == "$device_id")
        |> last()
        |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")
        FLUX;

        $result = $queryApi->query($fluxQuery);

        $data = [];

        foreach ($result as $table) {
            foreach ($table->records as $record) {
                $data = $record->values;
            }
        }

        return $data;
    }

    function getDailyLogs($device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_AllTime")
        |> range(start: 0)
        |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
        |> filter(fn: (r) => r["Device_Id"] == "$device_id")
        |> pivot(rowKey: ["_time"], columnKey: ["_field"], valueColumn: "_value")
        |> sort(columns: ["_time"], desc: true)
        |> limit(n:4)
        |> keep(columns: ["Volume_delta", "_time", "_value", "_field", "Durasi_Operasional_delta", "Energi_delta"])
        FLUX;

        $result = $queryApi->query($fluxQuery);

        $data = [];

        foreach ($result as $table) {
            foreach ($table->records as $record) {
                $data[] = $record->values;
            }
        }

        return $data;
    }

    public function getFirstData($lastTime, $device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        if ($lastTime instanceof \DateTime || $lastTime instanceof \DateTimeImmutable) {
            $dt = \DateTime::createFromInterface($lastTime);
        } else {
            $dt = new \DateTime((string) $lastTime);
        }

        $dt->setTimezone(new \DateTimeZone('UTC'));

        $startOfDay = (clone $dt)->setTime(0, 0, 0)->format('Y-m-d\TH:i:s\Z');
        $endOfDay   = (clone $dt)->setTime(0, 0, 0)->modify('+1 day')->format('Y-m-d\TH:i:s\Z');

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
        |> range(start: $startOfDay, stop: $endOfDay)
        |> filter(fn: (r) => r._measurement == "IoT_PTSP" and r.Device_Id == "$device_id")
        |> filter(fn: (r) => r._field == "Volume" or r._field == "Energi" or r._field == "Durasi_Operasional")
        |> first()
        |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")
        FLUX;

        $result = $queryApi->query($fluxQuery);

        $data = [];
        foreach ($result as $table) {
            foreach ($table->records as $record) {
                $data = $record->values;
            }
        }

        return [
            // 'time'                => $data['_time']              ?? null,
            'Energi'              => $data['Energi']             ?? null,
            'Volume'              => $data['Volume']             ?? null,
            'Durasi_Operasional'  => $data['Durasi_Operasional'] ?? null,
        ];
    }

    public function getAvgSuhu($lastTime, $device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        if ($lastTime instanceof \DateTime || $lastTime instanceof \DateTimeImmutable) {
            $dt = \DateTime::createFromInterface($lastTime);
        } else {
            $dt = new \DateTime((string) $lastTime);
        }

        $dt->setTimezone(new \DateTimeZone('UTC'));

        $startOfDay = (clone $dt)->setTime(0, 0, 0)->format('Y-m-d\TH:i:s\Z');
        $endOfDay   = (clone $dt)->setTime(0, 0, 0)->modify('+1 day')->format('Y-m-d\TH:i:s\Z');

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
        |> range(start: $startOfDay, stop: $endOfDay)
        |> filter(fn: (r) => r._measurement == "IoT_PTSP" and r.Device_Id == "$device_id")
        |> filter(fn: (r) => r._field == "Suhu")
        |> mean()
        FLUX;

        $result = $queryApi->query($fluxQuery);
        return $result[0]->records[0]->values["_value"];
    }

    public function tes($device_name, $device_id)
    {
        $device = Device::where("id", $device_id)->select('id', 'owner_id', 'device_name', 'online_status', 'created_at')->with('device_config')->first();

        return $device;
    }

    public function index($device_name, $device_id)
    {
        $device = Device::where("id", $device_id)->select('id', 'owner_id', 'device_name', 'online_status', 'created_at')->with('device_config')->first();
        $latest = $this->getLatestData($device_id);
        $logs = $this->getDailyLogs($device_id);
        $first = $this->getFirstData($latest['_time'], $device_id);
        $today = [
            "Energi" => $latest["Energi"] - $first["Energi"],
            "Volume" => $latest["Volume"] - $first["Volume"],
            "Durasi_Operasional" => $latest["Durasi_Operasional"] - $first["Durasi_Operasional"],
        ];
        $avgSuhu = $this->getAvgSuhu($latest['_time'], $device_id);

        $devices = Device::select('id', 'owner_id', 'serial_number', 'device_name', 'online_status')->where('owner_id', Auth::id())->get();
        $id = $device_id;

        // dd($device);
        return view('dashboard', compact('device', 'latest', 'logs', 'today', 'avgSuhu', 'devices', 'id'));
    }

    public function getChartData($device_id)
    {
        $queryApi = InfluxDB::createQueryApi();
        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
            |> range(start: 0)
            |> filter(fn: (r) => r._measurement == "IoT_PTSP")
            |> filter(fn: (r) => r["Device_Id"] == "$device_id")
            |> filter(fn: (r) => r._field == "Debit" or r._field == "Daya")
            |> sort(columns: ["_time"], desc: true)
            |> limit(n: 200)
            |> sort(columns: ["_time"], desc: false)
            |> yield(name: "results")
        FLUX;


        try {
            // 5. Eksekusi Query
            $tables = $queryApi->query($fluxQuery);

            // 6. Siapkan struktur dasar untuk JSON
            $seriesData = [
                'Debit' => [
                    'name' => 'Debit',
                    'data' => []
                ],
                'Daya' => [
                    'name' => 'Daya',
                    'data' => []
                ]
            ];

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $field = $record->getField(); // 'debit' atau 'daya'

                    // InfluxDB mereturn time dalam string ISO8601 (misal: "2023-10-27T10:00:00Z")
                    // Kita harus mengubahnya ke UNIX Epoch dalam milidetik (ms) untuk ApexCharts
                    $timeMs = strtotime($record->getTime()) * 1000;
                    $value = $record->getValue();

                    // Masukkan ke array field yang sesuai
                    if (isset($seriesData[$field])) {
                        $seriesData[$field]['data'][] = [
                            'x' => $timeMs,
                            'y' => is_numeric($value) ? (float) $value : null
                        ];
                    }
                }
            }

            // 8. Hapus key string 'debit' & 'daya', ubah menjadi array numerik biasa (karena ApexCharts butuh array of objects)
            $finalResponse = array_values($seriesData);

            // Return sebagai response JSON
            return response()->json($finalResponse, 200);
        } catch (\Exception $e) {
            // Tangani error jika koneksi/query InfluxDB gagal
            return response()->json([
                'error' => 'Gagal mengambil data dari InfluxDB',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
