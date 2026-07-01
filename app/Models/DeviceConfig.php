<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceConfig extends Model
{
    protected $table = 'devices_config';
    protected $fillable = [
        'device_id',
        'mode',
        'lat',
        'long',
        'location'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
