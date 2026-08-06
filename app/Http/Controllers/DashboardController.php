<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use RikoDEV\InfluxDB\Facades\InfluxDB;

class DashboardController extends Controller
{
    private const CUMULATIVE_FIELDS = ['Energi', 'Volume', 'Durasi_Operasional'];

    // ---------------------------------------------------------------
    // HELPERS INTI
    // ---------------------------------------------------------------

    /** Snapshot LENGKAP (semua field + _time) pada titik data terbaru di PTSP_Raw */
    function getLatestData($device_id)
    {
        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
        |> range(start: 0)
        |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
        |> filter(fn: (r) => r["Device_Id"] == "$device_id")
        |> last()
        |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")
        FLUX;

        $data = [];
        foreach (InfluxDB::createQueryApi()->query($fluxQuery) as $table) {
            foreach ($table->records as $record) {
                $data = $record->values;
            }
        }

        return $data;
    }

    /** Nilai 3 field kumulatif (Energi/Volume/Durasi_Operasional) pada/sebelum sebuah timestamp */
    private function getRawCumulativeValuesAt(\DateTimeInterface $at, string $device_id): array
    {
        $stop = \DateTime::createFromInterface($at)->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
        |> range(start: 0, stop: $stop)
        |> filter(fn: (r) => r._measurement == "IoT_PTSP" and r.Device_Id == "$device_id")
        |> filter(fn: (r) => r._field == "Energi" or r._field == "Volume" or r._field == "Durasi_Operasional")
        |> last()
        |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")
        FLUX;

        $data = [];
        foreach (InfluxDB::createQueryApi()->query($fluxQuery) as $table) {
            foreach ($table->records as $record) {
                $data = $record->values;
            }
        }

        return [
            'Energi'             => isset($data['Energi']) ? (float) $data['Energi'] : null,
            'Volume'             => isset($data['Volume']) ? (float) $data['Volume'] : null,
            'Durasi_Operasional' => isset($data['Durasi_Operasional']) ? (float) $data['Durasi_Operasional'] : null,
        ];
    }

    /** Sum field *_delta di PTSP_AllTime untuk rentang [start, stop) */
    private function sumDeltaForRange(string $device_id, \DateTimeInterface $start, \DateTimeInterface $stop): array
    {
        $startRFC = \DateTime::createFromInterface($start)->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
        $stopRFC  = \DateTime::createFromInterface($stop)->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_AllTime")
        |> range(start: $startRFC, stop: $stopRFC)
        |> filter(fn: (r) => r._measurement == "IoT_PTSP" and r.Device_Id == "$device_id")
        |> filter(fn: (r) => r._field == "Energi_delta" or r._field == "Volume_delta" or r._field == "Durasi_Operasional_delta")
        |> sum()
        FLUX;

        $sums = ['Energi' => 0.0, 'Volume' => 0.0, 'Durasi_Operasional' => 0.0];
        foreach (InfluxDB::createQueryApi()->query($fluxQuery) as $table) {
            foreach ($table->records as $record) {
                $field = str_replace('_delta', '', $record->getField());
                if (isset($sums[$field])) {
                    $sums[$field] += (float) $record->getValue();
                }
            }
        }

        return $sums;
    }

    /** _time record delta terakhir sebelum $before -> tau sampai kapan bucket harian sudah "menutupi" data */
    private function getLastDownsampledTime(string $device_id, \DateTimeInterface $before): ?\DateTime
    {
        $stopRFC = \DateTime::createFromInterface($before)->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_AllTime")
        |> range(start: 0, stop: $stopRFC)
        |> filter(fn: (r) => r._measurement == "IoT_PTSP" and r.Device_Id == "$device_id")
        |> filter(fn: (r) => r._field == "Volume_delta")
        |> last()
        FLUX;

        foreach (InfluxDB::createQueryApi()->query($fluxQuery) as $table) {
            foreach ($table->records as $record) {
                return new \DateTime($record->getTime(), new \DateTimeZone('UTC'));
            }
        }

        return null;
    }

    private function getGuardedDelta(
        string $device_id,
        \DateTimeInterface $start,
        \DateTimeInterface $stop,
        ?array $stopRawValues = null
    ): array {
        $start = \DateTime::createFromInterface($start)->setTimezone(new \DateTimeZone('UTC'));
        $stop  = \DateTime::createFromInterface($stop)->setTimezone(new \DateTimeZone('UTC'));

        $lastDownsampledTime = $this->getLastDownsampledTime($device_id, $stop);
        // asumsi: 1 record delta = akumulasi 1 hari penuh sejak _time
        // (task Downsample_PTSP_Raw_to_AllTime, timeSrc: "_start")
        $coveredUntil = $lastDownsampledTime !== null ? (clone $lastDownsampledTime)->modify('+1 day') : null;

        $sumDelta = ['Energi' => 0.0, 'Volume' => 0.0, 'Durasi_Operasional' => 0.0];
        $gapStart = $start;

        if ($coveredUntil !== null && $coveredUntil > $start) {
            $deltaStop = min($coveredUntil, $stop);
            $sumDelta  = $this->sumDeltaForRange($device_id, $start, $deltaStop);
            $gapStart  = $deltaStop;
        }

        $rawDiff = ['Energi' => 0.0, 'Volume' => 0.0, 'Durasi_Operasional' => 0.0];
        if ($gapStart < $stop) {
            $rawStart = $this->getRawCumulativeValuesAt($gapStart, $device_id);
            $rawStop  = $stopRawValues ?? $this->getRawCumulativeValuesAt($stop, $device_id);

            foreach (self::CUMULATIVE_FIELDS as $f) {
                if ($rawStart[$f] !== null && $rawStop[$f] !== null) {
                    $rawDiff[$f] = max(0, $rawStop[$f] - $rawStart[$f]); // guard: reset counter/reboot device
                }
            }
        }

        $delta = [];
        foreach (self::CUMULATIVE_FIELDS as $f) {
            $delta[$f] = $sumDelta[$f] + $rawDiff[$f];
        }

        return $delta;
    }

    /** first = latest - delta_gabungan, biar FE yang subtract naive tetap dapat angka yang benar */
    private function reconstructFirst(array $latestFull, array $delta): array
    {
        $first = [];
        foreach (self::CUMULATIVE_FIELDS as $f) {
            $first[$f] = isset($latestFull[$f]) ? ((float) $latestFull[$f] - $delta[$f]) : null;
        }
        return $first;
    }

    // ---------------------------------------------------------------
    // ENDPOINTS
    // ---------------------------------------------------------------

    /**
     * GET /device/{device_id}/kinerja-baseline?range=1h|1m|custom&start=&stop=
     * dipakai oleh: refreshBaseline(range) di dashboard script
     */
    public function kinerjaBaseline(Request $request, string $device_id): JsonResponse
    {
        $range = $request->query('range', '1h');

        $latestFull = $this->getLatestData($device_id);
        if (empty($latestFull) || !isset($latestFull['_time'])) {
            return response()->json(['error' => 'Device belum memiliki data'], 404);
        }
        $stop = new \DateTime((string) $latestFull['_time']);

        // TAMBAHAN: anggap custom kalau ada 'start' di query, walau 'range' tidak dikirim
        if ($range === 'custom' || $request->has('start')) {
            $validated = $request->validate([
                'start' => 'required|date',
                'stop'  => 'nullable|date',
            ]);
            $start = new \DateTime($validated['start']);
            if (!empty($validated['stop'])) {
                $stop = new \DateTime($validated['stop']);
            }
        } else {
            if (!in_array($range, ['1h', '1m'], true)) {
                $range = '1h';
            }
            $durationDays = $range === '1m' ? 7 : 1;
            $start = (clone $stop)->modify("-{$durationDays} day");
        }

        $stopRawValues = array_intersect_key($latestFull, array_flip(self::CUMULATIVE_FIELDS));
        $delta = $this->getGuardedDelta($device_id, $start, $stop, $stopRawValues);
        $first = $this->reconstructFirst($latestFull, $delta);

        return response()->json([
            'first'  => $first,
            'latest' => $latestFull,
        ]);
    }

    /**
     * GET /device/{device_id}/session-baseline?job_created=
     * dipakai oleh: window.initSessionCard(card)
     */
    public function sessionBaseline(Request $request, string $device_id): JsonResponse
    {
        $validated = $request->validate([
            'job_created' => 'required|date',
        ]);

        $latestFull = $this->getLatestData($device_id);
        if (empty($latestFull) || !isset($latestFull['_time'])) {
            return response()->json(['error' => 'Device belum memiliki data'], 404);
        }

        $start = new \DateTime($validated['job_created']);
        $stop  = new \DateTime((string) $latestFull['_time']);

        $stopRawValues = array_intersect_key($latestFull, array_flip(self::CUMULATIVE_FIELDS));
        $delta    = $this->getGuardedDelta($device_id, $start, $stop, $stopRawValues);
        $baseline = $this->reconstructFirst($latestFull, $delta);

        return response()->json([
            'device_id' => $device_id,
            'baseline'  => $baseline,
        ]);
    }

    private function calculateDiff(array $latest, array $first): array
    {
        return [
            "Energi"             => $latest["Energi"] - $first["Energi"],
            "Volume"             => $latest["Volume"] - $first["Volume"],
            "Durasi_Operasional" => $latest["Durasi_Operasional"] - $first["Durasi_Operasional"],
        ];
    }

    public function getAvgSuhu($lastTime, $device_id)
    {
        // tetap sama seperti sebelumnya
        $dt = $lastTime instanceof \DateTimeInterface ? \DateTime::createFromInterface($lastTime) : new \DateTime((string) $lastTime);
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

        $result = InfluxDB::createQueryApi()->query($fluxQuery);
        return $result[0]->records[0]->values["_value"];
    }

    public function index($device_name, $device_id)
    {
        $device = Device::where("id", $device_id)->select('id', 'owner_id', 'device_name', 'online_status', 'serial_number', 'created_at')->with('device_config')->first();
        $logs   = $this->getDailyLogs($device_id);

        $latestFull = $this->getLatestData($device_id);
        $stop = new \DateTime((string) $latestFull['_time']);
        $start = (clone $stop)->modify('-1 day');

        $stopRawValues = array_intersect_key($latestFull, array_flip(self::CUMULATIVE_FIELDS));
        $delta = $this->getGuardedDelta($device_id, $start, $stop, $stopRawValues);
        $first = $this->reconstructFirst($latestFull, $delta);

        $today   = $this->calculateDiff($latestFull, $first); // = $delta, tapi dihitung ulang biar konsisten sama view lama
        $avgSuhu = $this->getAvgSuhu($latestFull['_time'], $device_id);

        $devices = Device::select('id', 'owner_id', 'serial_number', 'device_name', 'online_status')->where('owner_id', Auth::id())->get();
        $id   = $device_id;
        $name = $device_name;

        return view('dashboard', compact('device', 'latestFull', 'logs', 'today', 'first', 'avgSuhu', 'devices', 'id', 'name') + ['latest' => $latestFull]);
    }

    public function getChartData(Request $request, $device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        $allowedFields = ['Daya', 'Debit', 'Durasi_Operasional', 'Energi', 'Suhu', 'Tegangan', 'Volume'];

        // Field yang nilai mentahnya adalah counter kumulatif (terus naik).
        // Untuk field ini kita pakai versi "_delta" (di bucket PTSP_Raw) lalu
        // di-cumulativeSum() ulang supaya chart mulai dari ~0 di setiap window.
        $cumulativeFields = ['Volume', 'Durasi_Operasional', 'Energi'];

        // Field bucket PTSP_AllTime (branch harian) tetap pakai suffix "_total"
        // seperti sebelumnya -- lihat catatan di atas kenapa branch ini tidak diubah.
        $totalSuffixFields = $cumulativeFields;

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

        // Pisahkan field yang diminta jadi 2 kelompok: gauge (apa adanya) & kumulatif (delta)
        $gaugeFieldsReq = array_values(array_diff($fields, $cumulativeFields));
        $cumFieldsReq   = array_values(array_intersect($fields, $cumulativeFields));

        $fieldFilter = implode(' or ', array_map(fn($f) => "r._field == \"$f\"", $fields));

        $fieldFilterAllTime = implode(' or ', array_map(function ($f) use ($totalSuffixFields) {
            $actual = in_array($f, $totalSuffixFields) ? $f . '_total' : $f;
            return "r._field == \"$actual\"";
        }, $fields));

        // ---- Validasi range ----
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
            if ($range === 'CUSTOM' && $hasCustomDateRange) {
                $startRFC3339 = gmdate('Y-m-d\T00:00:00\Z', $startTsRaw);
                $stopRFC3339  = gmdate('Y-m-d\T00:00:00\Z', strtotime('+1 day', $endTsRaw));

                if ($resolution === 'harian') {
                    // Bucket long-term retention, agregat harian.
                    // TIDAK DIUBAH -- lihat catatan di docblock atas.
                    $fluxQuery = <<<FLUX
                from(bucket: "PTSP_AllTime")
                    |> range(start: $startRFC3339, stop: $stopRFC3339)
                    |> filter(fn: (r) => r._measurement == "IoT_PTSP")
                    |> filter(fn: (r) => r["Device_Id"] == "$device_id")
                    |> filter(fn: (r) => $fieldFilterAllTime)
                    |> aggregateWindow(every: 1d, fn: mean, createEmpty: false)
                    |> sort(columns: ["_time"], desc: false)
                    |> yield(name: "results")
                FLUX;
                } else {
                    // Bucket raw, rentang tanggal custom penuh (tanpa agregasi).
                    // Field kumulatif -> ambil versi _delta + cumulativeSum() ulang.
                    $fluxQuery = $this->buildRawFluxQuery(
                        "range(start: $startRFC3339, stop: $stopRFC3339)",
                        $device_id,
                        $gaugeFieldsReq,
                        $cumFieldsReq
                    );
                }
            } elseif ($range === 'CUSTOM') {
                // Custom tanpa start/end valid -> fallback 250 titik terakhir dari Raw bucket
                $fluxQuery = $this->buildRawFluxQuery(
                    'range(start: 0)',
                    $device_id,
                    $gaugeFieldsReq,
                    $cumFieldsReq,
                    250 // limit N titik terakhir per field, sebelum cumulativeSum
                );
            } else {
                // 1H / 1M -> cari waktu data terakhir dulu (pakai nama field asli,
                // cukup untuk menentukan timestamp, tidak perlu bedakan gauge/delta)
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
                    return response()->json([], 200);
                }

                $durationDays = $range === '1M' ? 7 : 1;
                $stopTs  = strtotime($lastTime);
                $startTs = $stopTs - ($durationDays * 86400);

                $startRFC3339 = gmdate('Y-m-d\TH:i:s\Z', $startTs);
                $stopRFC3339  = gmdate('Y-m-d\TH:i:s\Z', $stopTs + 1);

                $fluxQuery = $this->buildRawFluxQuery(
                    "range(start: $startRFC3339, stop: $stopRFC3339)",
                    $device_id,
                    $gaugeFieldsReq,
                    $cumFieldsReq
                );
            }

            $tables = $queryApi->query($fluxQuery);

            $seriesData = [];
            foreach ($fields as $field) {
                $seriesData[$field] = ['name' => $field, 'data' => []];
            }

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $field = $record->getField();

                    // Strip suffix "_total" (bucket AllTime) atau "_delta" (bucket Raw,
                    // hasil cumulativeSum ulang) supaya kembali ke nama field asli.
                    foreach (['_total', '_delta'] as $suffix) {
                        if (str_ends_with($field, $suffix)) {
                            $field = substr($field, 0, -strlen($suffix));
                            break;
                        }
                    }

                    $timeMs = strtotime($record->getTime()) * 1000;
                    $value = $record->getValue();

                    // Durasi_Operasional disimpan dalam detik -> tampilkan dalam menit.
                    // Ini tetap valid walau nilainya sekarang hasil cumulativeSum,
                    // karena konversi satuan diterapkan per-titik secara konsisten.
                    if ($field === 'Durasi_Operasional' && is_numeric($value)) {
                        $value = $value / 60;
                    }

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

    private function buildRawFluxQuery(
        string $rangeClause,
        string $deviceId,
        array $gaugeFields,
        array $cumFields,
        ?int $limitLastN = null
    ): string {
        $streams = [];

        if (!empty($gaugeFields)) {
            $filter = implode(' or ', array_map(fn($f) => "r._field == \"$f\"", $gaugeFields));

            $q = "from(bucket: \"PTSP_Raw\")\n"
                . "    |> $rangeClause\n"
                . "    |> filter(fn: (r) => r._measurement == \"IoT_PTSP\")\n"
                . "    |> filter(fn: (r) => r[\"Device_Id\"] == \"$deviceId\")\n"
                . "    |> filter(fn: (r) => $filter)\n";

            $q .= $limitLastN
                ? "    |> sort(columns: [\"_time\"], desc: true)\n    |> limit(n: $limitLastN)\n    |> sort(columns: [\"_time\"], desc: false)"
                : "    |> sort(columns: [\"_time\"], desc: false)";

            $streams['gauge'] = $q;
        }

        if (!empty($cumFields)) {
            $filter = implode(' or ', array_map(fn($f) => "r._field == \"{$f}_delta\"", $cumFields));

            $q = "from(bucket: \"PTSP_Raw\")\n"
                . "    |> $rangeClause\n"
                . "    |> filter(fn: (r) => r._measurement == \"IoT_PTSP\")\n"
                . "    |> filter(fn: (r) => r[\"Device_Id\"] == \"$deviceId\")\n"
                . "    |> filter(fn: (r) => $filter)\n";

            $q .= $limitLastN
                ? "    |> sort(columns: [\"_time\"], desc: true)\n    |> limit(n: $limitLastN)\n    |> sort(columns: [\"_time\"], desc: false)\n    |> cumulativeSum()"
                : "    |> sort(columns: [\"_time\"], desc: false)\n    |> cumulativeSum()";

            $streams['cum'] = $q;
        }

        // Kalau cuma satu jenis stream yang diminta, tidak perlu union.
        if (count($streams) === 1) {
            return array_values($streams)[0] . "\n    |> yield(name: \"results\")";
        }

        // Gabungkan kedua stream lewat union(), lalu sort ulang gabungannya.
        $assignments = '';
        $vars = [];
        $i = 0;
        foreach ($streams as $body) {
            $var = "s{$i}";
            $vars[] = $var;
            $assignments .= "$var =\n    $body\n\n";
            $i++;
        }

        return $assignments
            . 'union(tables: [' . implode(', ', $vars) . "])\n"
            . "    |> sort(columns: [\"_time\"], desc: false)\n"
            . "    |> yield(name: \"results\")";
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
}
