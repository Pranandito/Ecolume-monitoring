<?php

namespace App\Services;

use App\Models\Device;
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
        $device = Device::where('id', $device_id)->select('id', 'online_status')
            ->with(['device_config' => function ($querry) {
                $querry->select('id', 'device_id', 'job_id', 'job_confirmed', 'mode', 'volume_progress', 'volume_limit', 'timer_start', 'timer_end');
            }])->first();

        // $deviceConfig = DeviceConfig::where('device_id', $device_id)
        //     ->select('device_id', 'job_id', 'job_confirmed', 'mode', 'volume_progress', 'volume_limit', 'timer_start', 'timer_end')
        //     ->first();

        if ($device->device_config && $message == $device->device_config->job_id) {
            $updated = DeviceConfig::where('device_id', $device_id)
                ->update([
                    'job_confirmed' => 1
                ]);
        } else {
            $updated = false;
            $this->mqttPublish->publishMode($device_id, $serial_number, $device->device_config, 1);
        }

        if ($device->online_status == 0) {
            $this->statusUpdate->DeviceOnlineStatusUpdate($device_id);
        }

        return $updated;
    }
}
