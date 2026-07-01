<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'owner_id',
        'device_name',
        'serial_number',
        'firmware_version',
        'API_keys',
        'online_status'
    ];

    public function device_config()
    {
        return $this->hasOne(DeviceConfig::class);
    }
}
