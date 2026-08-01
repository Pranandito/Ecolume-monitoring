<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceConfig;

class StatusUpdate
{
    public function DeviceStatusUpdate($device_id)
    {
        $update = Device::where('id', $device_id)->update(["online_status" => 1]);
    }
}
