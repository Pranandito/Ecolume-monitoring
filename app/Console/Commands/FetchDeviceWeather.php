<?php

namespace App\Console\Commands;

use App\Models\DailyWeatherForecast;
use App\Models\Device;
use App\Models\HourlyWeatherForecast;
use App\Models\TodayWeather;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchDeviceWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch weather data (today, daily forecast, hourly forecast) untuk semua device dan simpan ke database';

    /**
     * Execute the console command.
     */
    private const OPEN_METEO_URL = 'https://api.open-meteo.com/v1/forecast';

    public function handle(): int
    {
        $devices = Device::whereNotNull('owner_id')
            ->select('id', 'owner_id')
            ->with(['device_config' => function ($query) {
                $query->select('id', 'device_id', 'lat', 'long');
            }])->get();

        if ($devices->isEmpty()) {
            $this->info('Tidak ada device untuk dicek.');
            return self::SUCCESS;
        }

        foreach ($devices as $device) {
            if (!$device->device_config) {
                continue;
            }

            // --- Fetch dari API, dipisah try-catch sendiri supaya kalau gagal
            // ketahuan jelas API mana yang error, dan proses delete/insert
            // di bawah TIDAK PERNAH jalan kalau fetch gagal.
            try {
                $todayWeather = $this->fetchTodayWeather($device->device_config->lat, $device->device_config->long);
            } catch (\Throwable $e) {
                Log::error("Gagal fetch TODAY weather untuk device {$device->id}: " . $e->getMessage());
                $this->error("Device {$device->id}: gagal fetch today weather - " . $e->getMessage());
                continue;
            }

            try {
                $weatherForecast = $this->fetchWeatherForecast($device->device_config->lat, $device->device_config->long);
            } catch (\Throwable $e) {
                Log::error("Gagal fetch FORECAST weather untuk device {$device->id}: " . $e->getMessage());
                $this->error("Device {$device->id}: gagal fetch weather forecast - " . $e->getMessage());
                continue;
            }

            // Validasi struktur data sebelum lanjut ke delete+insert
            if (
                empty($todayWeather['daily']['time']) ||
                empty($weatherForecast['daily']['time']) ||
                empty($weatherForecast['hourly']['time'])
            ) {
                Log::warning("Struktur data weather tidak lengkap untuk device {$device->id}, dilewati.");
                $this->warn("Device {$device->id}: struktur data tidak lengkap, dilewati.");
                continue;
            }

            // --- Baru sampai sini delete+insert dijalankan, karena fetch sudah pasti sukses ---
            try {
                $daily = $todayWeather['daily'];
                $dailyForecast = $weatherForecast['daily'];
                $hourlyForecast = $weatherForecast['hourly'];

                TodayWeather::where('device_id', $device->id)->delete();
                DailyWeatherForecast::where('device_id', $device->id)->delete();
                HourlyWeatherForecast::where('device_id', $device->id)->delete();

                TodayWeather::updateOrCreate(
                    [
                        'device_id' => $device->id,
                        'date' => $daily['time'][0],
                    ],
                    [
                        'precipitation_probability_mean' => $daily['precipitation_probability_mean'][0],
                        'relative_humidity_mean' => $daily['relative_humidity_2m_mean'][0],
                        'wind_speed_mean' => $daily['windspeed_10m_mean'][0],
                        'shortwave_radiation_sum' => $daily['shortwave_radiation_sum'][0],
                        'cloud_cover_mean' => $daily['cloudcover_mean'][0],
                        'sunrise' => $daily['sunrise'][0],
                        'sunset' => $daily['sunset'][0],
                        'temperature_mean' => $daily['temperature_2m_mean'][0],
                        'apparent_temperature_mean' => $daily['apparent_temperature_mean'][0],
                        'weather_code' => $dailyForecast['weather_code'][0],
                    ]
                );

                for ($i = 0; $i < count($dailyForecast['time']); $i++) {
                    DailyWeatherForecast::updateOrCreate(
                        [
                            'device_id' => $device->id,
                            'date' => $dailyForecast['time'][$i],
                        ],
                        [
                            'temperature_mean' => $dailyForecast['temperature_2m_mean'][$i],
                            'weather_code' => $dailyForecast['weather_code'][$i],
                        ]
                    );
                }

                for ($i = 0; $i < count($hourlyForecast['time']); $i++) {
                    HourlyWeatherForecast::updateOrCreate(
                        [
                            'device_id' => $device->id,
                            'datetime' => $hourlyForecast['time'][$i],
                        ],
                        [
                            'temperature' => $hourlyForecast['temperature_2m'][$i],
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error("Gagal simpan weather untuk device {$device->id}: " . $e->getMessage());
                $this->error("Device {$device->id}: gagal simpan data - " . $e->getMessage());
                continue;
            }
        }

        $this->info('Selesai fetch weather data.');
        return self::SUCCESS;
    }

    private function fetchTodayWeather($lat, $long)
    {
        $response = Http::timeout(30)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $long,
            'daily' => implode(',', [
                'precipitation_probability_mean',
                'relative_humidity_2m_mean',
                'windspeed_10m_mean',
                'shortwave_radiation_sum',
                'cloudcover_mean',
                'sunrise',
                'sunset',
                'temperature_2m_mean',
                'apparent_temperature_mean',
            ]),
            'timezone' => 'Asia/Jakarta',
            'past_days' => 0,
            'forecast_days' => 1,
        ]);

        $response->throw();

        return $response;
    }

    private function fetchWeatherForecast($lat, $long)
    {
        $response = Http::timeout(30)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $long,
            'daily' => implode(',', [
                'temperature_2m_mean',
                'weather_code',
            ]),
            'hourly' => 'temperature_2m',
            'timezone' => 'Asia/Jakarta',
            'forecast_days' => 5,
        ]);

        $response->throw();

        return $response;
    }
}
