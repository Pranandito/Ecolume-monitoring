<?php

namespace App\Models;

use App\Enums\WeatherCode;
use Illuminate\Database\Eloquent\Model;

class TodayWeather extends Model
{
    protected $fillable = [
        'device_id',
        'date',
        'precipitation_probability_mean',
        'relative_humidity_mean',
        'wind_speed_mean',
        'shortwave_radiation_sum',
        'cloud_cover_mean',
        'sunrise',
        'sunset',
        'temperature_mean',
        'apparent_temperature_mean',
        'weather_code',
    ];

    protected $casts = [
        'date' => 'date',
        'sunrise' => 'datetime',
        'sunset' => 'datetime',
        'precipitation_probability_mean' => 'float',
        'relative_humidity_mean' => 'float',
        'wind_speed_mean' => 'float',
        'shortwave_radiation_sum' => 'float',
        'cloud_cover_mean' => 'float',
        'temperature_mean' => 'float',
        'apparent_temperature_mean' => 'float',
        'weather_code' => WeatherCode::class,
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
