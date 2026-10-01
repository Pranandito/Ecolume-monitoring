<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firmware extends Model
{
    protected $table = 'firmwares';
    protected $fillable = [
        'file_path',
        'firmware_ver',
        'firmware_code',
        'size',
        'uploader',
        'is_prod'
    ];

    public function device()
    {
        return $this->hasMany('devices', 'id', 'firmware_id');
    }
}
