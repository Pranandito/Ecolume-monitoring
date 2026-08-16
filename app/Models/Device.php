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
        'online_status',
        'claim_at'
    ];

    public function device_config()
    {
        return $this->hasOne(DeviceConfig::class);
    }

    public function today_weather()
    {
        return $this->hasOne(TodayWeather::class);
    }

    public function daily_weather_forecast()
    {
        return $this->hasMany(DailyWeatherForecast::class);
    }

    public function hourly_weather_forecast()
    {
        return $this->hasMany(HourlyWeatherForecast::class);
    }
}
