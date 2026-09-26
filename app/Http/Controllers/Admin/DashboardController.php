<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RikoDEV\InfluxDB\Facades\InfluxDB;
use stdClass;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::select('id', 'last_seen', 'online_status', 'owner_id', 'device_name')->with(['device_config' => function ($querry) {
            $querry->select('id', 'device_id', 'lat', 'long', 'location');
        }])->get();

        $count = new stdClass;
        $count->online = $devices->where('online_status', 1)->count();
        $count->offline = $devices->count() - $count->online;
        $count->onlineToday = $devices->filter(fn($device) =>
        $device->last_seen !== null && Carbon::parse($device->last_seen)
            ->isToday())->count();

        $devices = $devices->whereNotNull('owner_id');

        $adminList = User::where('role', 'admin')->select('id', 'name', 'email', 'role', 'role_updated_at')->get();

        $deviceList = Device::with(['user' => function ($querry) {
            $querry->select('id', 'name');
        }])->paginate(5);


        $activityLogs = ActivityLog::select('id', 'created_at', 'title', 'desc')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('devices', 'count', 'adminList', 'deviceList', 'activityLogs'));
    }

    public function getChartData(Request $request)
    {
        $queryApi = InfluxDB::createQueryApi();

        // ---- Ambil device yang ownernya tidak null ----
        $devices = Device::whereNotNull('owner_id')
            ->get(['id', 'device_name'])
            ->keyBy('id');

        if ($devices->isEmpty()) {
            return response()->json([], 200);
        }

        $deviceFilter = implode(' or ', $devices->keys()
            ->map(fn($id) => "r[\"Device_Id\"] == \"$id\"")
            ->toArray());

        // ---- Validasi range & resolution (sama seperti chart per-device) ----
        $range = $request->query('range', '1H');
        if (!in_array($range, ['1H', '1M', 'CUSTOM'])) {
            $range = '1H';
        }

        $resolution = $request->query('resolution', 'detail');
        if (!in_array($resolution, ['detail', 'harian'])) {
            $resolution = 'detail';
        }

        $startParam = $request->query('start');
        $endParam   = $request->query('end');

        $hasCustomDateRange = false;
        if ($range === 'CUSTOM' && $startParam && $endParam) {
            $startTsRaw = strtotime($startParam);
            $endTsRaw   = strtotime($endParam);

            if ($startTsRaw !== false && $endTsRaw !== false && $startTsRaw <= $endTsRaw) {
                $hasCustomDateRange = true;
            }
        }

        try {
            // ---- Tentukan rentang waktu & window agregasi ----
            if ($range === 'CUSTOM' && $hasCustomDateRange) {
                $startRFC3339 = gmdate('Y-m-d\T00:00:00\Z', $startTsRaw);
                $stopRFC3339  = gmdate('Y-m-d\T00:00:00\Z', strtotime('+1 day', $endTsRaw));
                $spanDays = ($endTsRaw - $startTsRaw) / 86400;
                $window = $spanDays <= 2 ? '15m' : ($spanDays <= 14 ? '1h' : '1d');
            } elseif ($range === 'CUSTOM') {
                // Custom tanpa start/end valid -> fallback 1 hari terakhir
                $stopTs   = time();
                $startTs  = $stopTs - 86400;
                $startRFC3339 = gmdate('Y-m-d\TH:i:s\Z', $startTs);
                $stopRFC3339  = gmdate('Y-m-d\TH:i:s\Z', $stopTs);
                $window = '15m';
            } else {
                // 1H (1 hari) / 1M (7 hari) -> cari waktu data terakhir dulu
                $lastTimeFlux = <<<FLUX
                from(bucket: "PTSP_Raw")
                    |> range(start: 0)
                    |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                    |> filter(fn: (r) => $deviceFilter)
                    |> filter(fn: (r) => r._field == "Durasi_Operasional_delta")
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
                    return response()->json([], 200);
                }

                $durationDays = $range === '1M' ? 7 : 1;
                $stopTs  = strtotime($lastTime);
                $startTs = $stopTs - ($durationDays * 86400);

                $startRFC3339 = gmdate('Y-m-d\TH:i:s\Z', $startTs);
                $stopRFC3339  = gmdate('Y-m-d\TH:i:s\Z', $stopTs + 1);
                $window = $range === '1M' ? '1h' : '15m';
            }

            // ---- Bangun flux query ----
            if ($resolution === 'harian') {
                // Bucket long-term retention, agregat harian per device.
                $fluxQuery = <<<FLUX
                from(bucket: "PTSP_AllTime")
                    |> range(start: $startRFC3339, stop: $stopRFC3339)
                    |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                    |> filter(fn: (r) => $deviceFilter)
                    |> filter(fn: (r) => r._field == "Durasi_Operasional_total")
                    |> group(columns: ["Device_Id"])
                    |> aggregateWindow(every: 1d, fn: mean, createEmpty: true)
                    |> fill(usePrevious: true)
                    |> sort(columns: ["_time"], desc: false)
                    |> yield(name: "results")
                FLUX;
            } else {
                // Bucket raw: field kumulatif -> pakai versi _delta lalu cumulativeSum(),
                // baru diselaraskan ke grid waktu (aggregateWindow) supaya bisa dijumlah
                // lintas device untuk series "Total".
                $fluxQuery = <<<FLUX
                from(bucket: "PTSP_Raw")
                    |> range(start: $startRFC3339, stop: $stopRFC3339)
                    |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                    |> filter(fn: (r) => $deviceFilter)
                    |> filter(fn: (r) => r._field == "Durasi_Operasional_delta")
                    |> group(columns: ["Device_Id"])
                    |> cumulativeSum()
                    |> aggregateWindow(every: $window, fn: last, createEmpty: true)
                    |> fill(usePrevious: true)
                    |> sort(columns: ["_time"], desc: false)
                    |> yield(name: "results")
                FLUX;
            }

            $tables = $queryApi->query($fluxQuery);

            // ---- Proses hasil: kumpulkan per-device, sekaligus akumulasi total per timestamp ----
            $seriesByDevice = [];
            $totalByTime = []; // [timeMs => sum]

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $deviceId = $record->values['Device_Id'] ?? null;
                    if ($deviceId === null || !isset($devices[$deviceId])) {
                        continue;
                    }

                    $value = $record->getValue();
                    if (!is_numeric($value)) {
                        continue;
                    }

                    // Durasi_Operasional disimpan dalam detik -> tampilkan dalam menit.
                    $valueMinutes = $value / 60;
                    $timeMs = strtotime($record->getTime()) * 1000;

                    if (!isset($seriesByDevice[$deviceId])) {
                        $seriesByDevice[$deviceId] = [
                            'name' => $devices[$deviceId]->name ?? $deviceId,
                            'data' => [],
                        ];
                    }

                    $seriesByDevice[$deviceId]['data'][] = [
                        'x' => $timeMs,
                        'y' => $valueMinutes,
                    ];

                    $totalByTime[$timeMs] = ($totalByTime[$timeMs] ?? 0) + $valueMinutes;
                }
            }

            ksort($totalByTime);
            $totalSeries = [
                'name' => 'Total',
                'data' => array_map(
                    fn($t, $v) => ['x' => $t, 'y' => $v],
                    array_keys($totalByTime),
                    array_values($totalByTime)
                ),
            ];

            return response()->json(array_merge([$totalSeries], array_values($seriesByDevice)), 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data dari InfluxDB',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
