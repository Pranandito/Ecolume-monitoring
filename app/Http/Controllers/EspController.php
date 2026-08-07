<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EspController extends Controller
{
    public function getTunellingPort(Request $request, $id)
    {
        $apiKey = $request->header('API_key');

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
}
