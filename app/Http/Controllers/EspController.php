<?php

namespace App\Http\Controllers;

use App\Models\DailyWeatherForecast;
use App\Models\Device;
use App\Models\DeviceConfig;
use App\Models\Firmware;
use App\Models\HourlyWeatherForecast;
use App\Models\TodayWeather;
use App\Services\Logger;
use App\Services\MQTTPublish;
use App\Services\StatusUpdate;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        protected StatusUpdate $statusUpdate,
        protected Logger $logger
    ) {}

    public function tes2(Request $request)
    {
        $validated = $request->validate([
            'firmware' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $name = strtolower($value->getClientOriginalName());
                    if (!str_ends_with($name, '.bin')) {
                        $fail('Format file harus .bin');
                    }
                }
            ],
            'firmware_ver' => 'required|string|regex:/^\d+\.\d+\.\d+$/'
        ]);

        $version_check = Firmware::where('firmware_ver', $validated['firmware_ver'])->first();
        if ($version_check) {
            // return back()->with([
            //     'status' => 'error',
            //     'title' => 'Terjadi Kesalahan',
            //     'desc' => 'Gagal menambahkan arsip firmware. Pastikan tidak ada duplikasi versi firmware'
            // ]);
            return "duplikasi versi";
        }

        $file = $request->file('firmware');
        $file_name = "firmware_v" . $validated['firmware_ver'] . ".bin";
        $validated['file_path'] = $file->storeAs('firmwares', $file_name, 'public');

        $firmware_codes = explode('.', $validated['firmware_ver']);
        $validated['firmware_code'] = $firmware_codes[0] * 1000000  + $firmware_codes[1] * 1000 + $firmware_codes[2] * 1;
        $validated['size'] = $file->getSize();

        $admin = "Didi";
        $validated['uploader'] = $admin;

        // $create = Firmware::create($validated);
        unset($validated['firmware']);
        $create = Firmware::create($validated);

        $data = [
            "admin_name" => $admin,
            "file_name" => $file_name
        ];

        $this->logger->create(2, "addFirmware", "firmwares", $create->id, $data, null);

        // return back()->with([
        //     'status' => 'success',
        //     'title' => 'Berhasil Ditambahkan!🎉',
        //     'desc' => 'Berhasil menambahkan arsip firmware. Broadcast flash fimrware ke hardware jika sudah siap'
        // ]);
        return $create;
    }
}
