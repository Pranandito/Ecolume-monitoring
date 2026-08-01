<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
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
        $device = Device::where("id", $device_id)->select('id', 'owner_id', 'device_name', 'online_status', 'serial_number', 'created_at')->with('device_config')->first();
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
        $name = $device_name;

        // dd($device);
        return view('dashboard', compact('device', 'latest', 'logs', 'today', 'avgSuhu', 'devices', 'id', 'name'));
    }

    public function getPumpPerformance($lastTime, $device_id)
    {
        $firstData = $this->getFirstData($lastTime, $device_id);

        return $firstData;
    }

    public function getChartData(Request $request, $device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        $allowedFields = ['Daya', 'Debit', 'Durasi_Operasional', 'Energi', 'Suhu', 'Tegangan', 'Volume'];

        // ---- Validasi fields ----
        $requestedFields = $request->query('fields');
        if (empty($requestedFields)) {
            $fields = ['Debit', 'Daya'];
        } else {
            if (is_string($requestedFields)) {
                $requestedFields = explode(',', $requestedFields);
            }
            $fields = array_values(array_intersect($allowedFields, $requestedFields));
            if (empty($fields)) {
                return response()->json([
                    'error' => 'Parameter fields tidak valid',
                    'allowed_fields' => $allowedFields
                ], 422);
            }
        }

        $fieldFilter = implode(' or ', array_map(fn($f) => "r._field == \"$f\"", $fields));

        // ---- Validasi range ----
        // 1H = 1 hari terakhir (relatif ke data terakhir, bukan hari kalender ini)
        // 1M = 7 hari terakhir (relatif ke data terakhir)
        // CUSTOM = 250 data point terakhir, tanpa batas waktu
        $range = $request->query('range', '1H');
        if (!in_array($range, ['1H', '1M', 'CUSTOM'])) {
            $range = '1H';
        }

        try {
            if ($range === 'CUSTOM') {
                // Ambil 250 data terakhir per-field
                $fluxQuery = <<<FLUX
            from(bucket: "PTSP_Raw")
                |> range(start: 0)
                |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                |> filter(fn: (r) => r["Device_Id"] == "$device_id")
                |> filter(fn: (r) => $fieldFilter)
                |> sort(columns: ["_time"], desc: true)
                |> limit(n: 250)
                |> sort(columns: ["_time"], desc: false)
                |> yield(name: "results")
            FLUX;
            } else {
                // Langkah 1: cari waktu data TERAKHIR yang benar-benar ada (bukan waktu sekarang),
                // supaya "1 hari" / "7 hari" dihitung mundur dari data, bukan dari jam kalender.
                $lastTimeFlux = <<<FLUX
            from(bucket: "PTSP_Raw")
                |> range(start: 0)
                |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                |> filter(fn: (r) => r["Device_Id"] == "$device_id")
                |> filter(fn: (r) => $fieldFilter)
                |> last()
            FLUX;

                $lastTables = $queryApi->query($lastTimeFlux);

                $lastTime = null;
                foreach ($lastTables as $table) {
                    foreach ($table->records as $record) {
                        $t = $record->getTime();
                        if ($lastTime === null || strtotime($t) > strtotime($lastTime)) {
                            $lastTime = $t;
                        }
                    }
                }

                if ($lastTime === null) {
                    // Device belum punya data sama sekali untuk field yang diminta
                    return response()->json([], 200);
                }

                $durationDays = $range === '1M' ? 7 : 1;
                $stopTs  = strtotime($lastTime);
                $startTs = $stopTs - ($durationDays * 86400);

                // Format RFC3339 (time literal Flux, tanpa tanda kutip)
                $startRFC3339 = gmdate('Y-m-d\TH:i:s\Z', $startTs);
                $stopRFC3339  = gmdate('Y-m-d\TH:i:s\Z', $stopTs + 1); // +1 detik agar titik data terakhir ikut

                $fluxQuery = <<<FLUX
                from(bucket: "PTSP_Raw")
                    |> range(start: $startRFC3339, stop: $stopRFC3339)
                    |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                    |> filter(fn: (r) => r["Device_Id"] == "$device_id")
                    |> filter(fn: (r) => $fieldFilter)
                    |> sort(columns: ["_time"], desc: false)
                    |> yield(name: "results")
                FLUX;
            }

            $tables = $queryApi->query($fluxQuery);

            $seriesData = [];
            foreach ($fields as $field) {
                $seriesData[$field] = ['name' => $field, 'data' => []];
            }

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $field = $record->getField();
                    $timeMs = strtotime($record->getTime()) * 1000;
                    $value = $record->getValue();

                    if (isset($seriesData[$field])) {
                        $seriesData[$field]['data'][] = [
                            'x' => $timeMs,
                            'y' => is_numeric($value) ? (float) $value : null
                        ];
                    }
                }
            }

            return response()->json(array_values($seriesData), 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data dari InfluxDB',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getHeatMapData($deviceId)
    {
        $queryApi = InfluxDB::createQueryApi();

        $fluxQuery = <<<FLUX
            from(bucket: "PTSP_AllTime")
            |> range(start: -30d)
            |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
            |> filter(fn: (r) => r["Device_Id"] == "{$deviceId}")
            |> filter(fn: (r) => r["_field"] == "Durasi_Operasional_delta" or r["_field"] == "Volume_total" or r["_field"] == "Volume_delta")
            |> pivot(rowKey: ["_time"], columnKey: ["_field"], valueColumn: "_value")
            |> sort(columns: ["_time"], desc: false)
            |> keep(columns: ["_time", "Durasi_Operasional_delta", "Volume_total", "Volume_delta"])
        FLUX;

        $tables = $queryApi->query($fluxQuery);

        $recordsByDate = [];
        $volumeFirst = null;
        $volumeLast = null;
        $count = 0;

        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $time = Carbon::parse($record->values['_time'])->timezone('Asia/Jakarta');
                $dateKey = $time->format('Y-m-d');

                $durasi = $record->values['Durasi_Operasional_delta'] ?? 0;
                $volume = $record->values['Volume_total'] ?? null;

                $recordsByDate[$dateKey] = $durasi > 0;
                if ($durasi > 0) {
                    $count++;
                }

                if ($volume !== null) {
                    $volumeFirst ??= $volume;
                    $volumeDeltaFirst ??= $record->values['Volume_delta'] ?? 0;
                    $volumeLast = $volume;
                }
            }
        }

        // Bangun kerangka 30 hari penuh, default tidak digunakan
        $heatmap = [];
        $start = Carbon::now('Asia/Jakarta')->subDays(29)->startOfDay();

        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');

            $heatmap[] = [
                'date' => $dateKey,
                'used' => $recordsByDate[$dateKey] ?? false,
            ];
        }

        $monthlyVolume = 0;
        if ($volumeFirst !== null && $volumeLast !== null) {
            $volumeFirst -= $volumeDeltaFirst;
            $selisihVolume = $volumeLast - $volumeFirst;
            $monthlyVolume = round($selisihVolume / 1000, 1);
        }

        return response()->json([
            'heatmap' => $heatmap,
            'volume_delta_30d' => $monthlyVolume,
            'count' => $count
        ]);
    }
}
