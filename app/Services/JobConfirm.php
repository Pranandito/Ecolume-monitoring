<?php

namespace App\Services;

use App\Models\DeviceConfig;
use App\Services\MQTTPublish;
use App\Services\StatusUpdate;


class JobConfirm
{
    public function __construct(
        protected MQTTPublish $mqttPublish,
        protected StatusUpdate $statusUpdate
    ) {}

    public function JobConfirm($message, $device_id, $serial_number)
    {
        $deviceConfig = DeviceConfig::where('device_id', $device_id)
            ->select('device_id', 'job_id', 'job_confirmed', 'mode', 'volume_progress', 'volume_limit', 'timer_start', 'timer_end')
            ->first();

        if ($deviceConfig && $message == $deviceConfig->job_id) {
            $updated = DeviceConfig::where('device_id', $device_id)
                ->update([
                    'job_confirmed' => 1
                ]);
        } else {
            $updated = false;
            $this->mqttPublish->publishMode($device_id, $serial_number, $deviceConfig, 1);
        }

        $this->statusUpdate->DeviceStatusUpdate($device_id);

        return $updated;
    }
}
