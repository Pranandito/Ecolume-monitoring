<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuacaController extends Controller
{
    public function index($device_name, $device_id)
    {
        $dev_id = $device_id;
        $dev_name = $device_name;
        $devices = Device::select('id', 'owner_id', 'serial_number', 'device_name', 'online_status')
            ->where('owner_id', Auth::id())->with(['device_config' => function ($querry) {
                $querry->select('id', 'device_id', 'location');
            }, 'today_weather' => function ($querry) {
                $querry->select('id', 'device_id', 'weather_code', 'temperature_mean');
            }])->get();
        $device = $devices->firstWhere('id', $dev_id);

        $deviceWeather = Device::where('id', $device_id)->select('id')
            ->with('today_weather', 'daily_weather_forecast', 'hourly_weather_forecast')->get();

        $deviceWeather = $deviceWeather[0];

        $sunDuration =  Carbon::parse($deviceWeather->today_weather->sunrise)
            ->diffInMinute($deviceWeather->today_weather->sunset);

        $deviceWeather->today_weather->sun_duration_h = floor($sunDuration / 60);
        $deviceWeather->today_weather->sun_duration_m = $sunDuration % 60;

        // return $deviceWeather->hourly_weather_forecast;


        return view('cuaca', compact('devices', 'device', 'deviceWeather', 'dev_id', 'dev_name'));
    }
}
