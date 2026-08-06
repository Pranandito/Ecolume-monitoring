<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceConfig extends Model
{
    protected $table = 'devices_config';
    protected $casts = [
        'job_created' => 'datetime',
    ];
    protected $fillable = [
        'device_id',
        'mode',
        'lat',
        'long',
        'location',
        'timer_start',
        'timer_end',
        'volume_limit',
        'volume_progress',
        'job_confirmed',
        'job_id',
        'job_created'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
