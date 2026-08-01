<?php

namespace App\Services;

use App\Models\DeviceConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class MQTTPublish
{
    private function timeToMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }

        [$hour, $minute] = array_map('intval', explode(':', $time));
        return $hour * 60 + $minute;
    }

    public function publishMode(string $device_id, string $serial_number, DeviceConfig $config, bool $isCommand): void
    {
        try {
            $payload = [
                'mode'   => $config->mode,
                'job_id' => $config->job_id,
            ];

            switch ($config->mode) {
                case 'Timer Waktu':
                    $payload['timer_start'] = $this->timeToMinutes($config->timer_start);
                    $payload['timer_end']   = $this->timeToMinutes($config->timer_end);
                    break;

                case 'Timer Volume':
                    $payload['volume_limit']    = $config->volume_limit;
                    $payload['volume_progress'] = $config->volume_progress;
                    break;

                case 'On':
                case 'Off':
                    break;
            }

            $mqtt = MQTT::connection('publisher');
            $mqtt->connect();
            $mqtt->publish("ecolumeIoT/mode/{$serial_number}/{$device_id}", json_encode($payload), 1, true);
            // if (!$isCommand) {
            //     $mqtt->disconnect();
            // }
        } catch (\Throwable $e) {
            Log::error("Gagal publish mode ke MQTT untuk device {$device_id}: {$e->getMessage()}");
        }
    }

    public function publishVolProgress($progress, $job_id, $serial_number, $device_id)
    {
        try {
            $payload = [
                'volume_progress'   => $progress,
                'job_id' => $job_id,
            ];

            $mqtt = MQTT::connection('publisher');
            $mqtt->connect();
            $mqtt->publish("ecolumeIoT/volume_progress/{$serial_number}/{$device_id}", json_encode($payload), 1, true);
            // $mqtt->disconnect();
        } catch (\Throwable $e) {
            Log::error("Gagal publish volume_progress ke MQTT untuk device {$device_id}: {$e->getMessage()}");
        }
    }
}
