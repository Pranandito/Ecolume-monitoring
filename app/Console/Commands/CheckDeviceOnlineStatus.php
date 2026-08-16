<?php

namespace App\Console\Commands;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RikoDEV\InfluxDB\Facades\InfluxDB;

class CheckDeviceOnlineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:status-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check dan update status online device setiap 5 menit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $devices = Device::whereNotNull('owner_id')
            ->select('id', 'owner_id', 'online_status')
            ->get();

        if ($devices->isEmpty()) {
            $this->info('Tidak ada device untuk dicek.');
            return self::SUCCESS;
        }

        // Query sekali untuk semua device, bukan N query terpisah
        $deviceIds = $devices->pluck('id')->map(fn($id) => '"' . $id . '"')->implode(', ');

        // return $deviceIds;

        $fluxQuery = <<<FLUX
                from(bucket: "PTSP_Raw")
                |> range(start: -2h)
                |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
                |> filter(fn: (r) => contains(value: r["Device_Id"], set: [$deviceIds]))
                |> keep(columns: ["_time", "Device_Id"])
                |> group(columns: ["Device_Id"])
                |> last(column: "_time")
            FLUX;

        $lastSeenMap = [];

        try {
            foreach (InfluxDB::createQueryApi()->query($fluxQuery) as $table) {
                foreach ($table->records as $record) {
                    $values = $record->values;
                    $lastSeenMap[$values['Device_Id']] = $values['_time'];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Gagal query InfluxDB untuk cek status device', [
                'error' => $e->getMessage(),
            ]);
            $this->error('Query InfluxDB gagal: ' . $e->getMessage());
            return self::FAILURE;
        }

        // return $lastSeenMap;

        $updates = [];

        foreach ($devices as $device) {
            $lastTime = $lastSeenMap[(string) $device->id] ?? null;

            if (!$lastTime) {
                $status = 0;
                $lastSeen = null;
            } else {
                $lastSeen = Carbon::parse($lastTime)
                    ->setTimezone('Asia/Jakarta')
                    ->diffInMinutes(now('Asia/Jakarta'));
                $status = $lastSeen > 5 ? 0 : 1;
            }

            if ($device->online_status != $status) {
                $updates[] = ['id' => $device->id, 'status' => $status];

                Log::info('Status device berubah', [
                    'device_id' => $device->id,
                    'old_status' => $device->online_status,
                    'new_status' => $status,
                    'last_seen_minutes' => $lastSeen,
                ]);
            }
        }

        foreach ($updates as $u) {
            Device::where('id', $u['id'])->update(['online_status' => $u['status']]);
        }

        $this->info(count($updates) . ' device berubah status dari ' . $devices->count() . ' total.');

        return self::SUCCESS;
        // return $updates;
    }
}
