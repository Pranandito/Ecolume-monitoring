<?php

namespace App\Models;

use App\Enums\WeatherCode;
use Illuminate\Database\Eloquent\Model;

class DailyWeatherForecast extends Model
{
    protected $fillable = [
        'device_id',
        'date',
        'temperature_mean',
        'weather_code',
    ];

    protected $casts = [
        'date' => 'date',
        'temperature_mean' => 'float',
        'weather_code' => WeatherCode::class
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
