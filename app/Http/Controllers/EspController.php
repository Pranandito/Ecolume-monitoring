<?php

namespace App\Http\Controllers;

use App\Models\DailyWeatherForecast;
use App\Models\Device;
use App\Models\DeviceConfig;
use App\Models\HourlyWeatherForecast;
use App\Models\TodayWeather;
use App\Services\MQTTPublish;
use App\Services\StatusUpdate;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RikoDEV\InfluxDB\Facades\InfluxDB;

class EspController extends Controller
{
    public function getTunellingPort(Request $request, $id)
    {
        $apiKey = $request->header('API-key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak ditemukan',
            ], 401);
        }

        $device = Device::where('id', $id)
            ->select('id', 'API_keys')
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan',
            ], 404);
        }

        if ($device->API_keys !== $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak cocok',
            ], 401);
        }

        try {
            $response = Http::timeout(3)
                ->get('http://127.0.0.1:4040/api/tunnels');
        } catch (ConnectionException $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Ngrok tidak dapat dihubungi',
            ], 503);
        }

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengakses API ngrok',
            ], 503);
        }

        $tunnels = $response->json('tunnels', []);

        foreach ($tunnels as $tunnel) {
            if (($tunnel['proto'] ?? null) === 'tcp') {

                $publicUrl = $tunnel['public_url'] ?? null;
                if (!$publicUrl) {
                    continue;
                }
                $url = parse_url($publicUrl);

                return response()->json([
                    'success' => true,
                    'port' => $url['port'] ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Tunnel TCP ngrok tidak ditemukan',
        ], 404);
    }

    public function __construct(
        protected StatusUpdate $statusUpdate
    ) {}

    public function tes2($device_id, $message)
    {
        $device = Device::where('id', $device_id)->select('id', 'online_status')
            ->with(['device_config' => function ($querry) {
                $querry->select('id', 'device_id', 'job_id', 'job_confirmed', 'mode', 'volume_progress', 'volume_limit', 'timer_start', 'timer_end');
            }])->first();

        // return $device;

        // $deviceConfig = DeviceConfig::where('device_id', $device_id)
        //     ->select('device_id', 'job_id', 'job_confirmed', 'mode', 'volume_progress', 'volume_limit', 'timer_start', 'timer_end')
        //     ->first();

        if ($device->device_config && $message == $device->device_config->job_id) {
            $updated = DeviceConfig::where('device_id', $device_id)
                ->update([
                    'job_confirmed' => 1
                ]);
        } else {
            $updated = false;
            // $this->mqttPublish->publishMode($device_id, $serial_number, $device->device_config, 1);
        }

        if ($device->online_status == 0) {
            $this->statusUpdate->DeviceOnlineStatusUpdate($device_id);
        }

        return $updated;
    }
}
