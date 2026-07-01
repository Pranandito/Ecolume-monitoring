<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class DeviceController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'firmware_version' => ['required', 'string', 'max:10'],
        ]);

        do {
            $serialNumber = 'PTSP-' . date('Y') . '-' . strtoupper(Str::random(5));
        } while (
            Device::where('serial_number', $serialNumber)->exists()
        );

        do {
            $apiKey = 'esp_' . Str::lower(Str::random(21));
        } while (
            Device::where('API_keys', $apiKey)->exists()
        );

        $device = Device::create([
            'firmware_version' => $request->firmware_version,
            'serial_number'    => $serialNumber,
            'API_keys'         => $apiKey,
        ]);

        $device_config = DeviceConfig::create([
            'device_id' => $device->id
        ]);

        return response()->json([
            'message' => 'Device berhasil dibuat',
            'data' => $device,
            'device_config' => $device_config
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $validatedData = $request->validate([
            'owner_id'         => ['nullable', 'exists:users,id'],
            'device_name'      => ['sometimes', 'required', 'string', 'max:25'],
            'online_status'    => ['sometimes', 'required', 'boolean'],
            'firmware_version' => ['sometimes', 'required', 'string', 'max:10'],
        ]);

        $device->update($validatedData);

        return response()->json([
            'message' => 'Device berhasil diupdate',
            'data'    => $device->fresh()
        ], 200);
    }

    public function tes()
    {
        $lat = '-7.2280831';
        $lng = '110.8538552';
        // Parameter zoom=14 biasanya pas untuk tingkat kecamatan/kota
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=14&addressdetails=1";

        // Nominatim mewajibkan header User-Agent yang valid
        $response = Http::withoutVerifying()->withHeaders([
            'User-Agent' => 'Ecolume-PTSP/1.0 (ditopranandito@gmail.com)'
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['address'])) {
                $address = $data['address'];

                // Backup bertingkat untuk Kota/Kabupaten
                $kotaKabupaten = $address['city']
                    ?? $address['town']
                    ?? $address['county']
                    ?? $address['municipality']
                    ?? $address['state_district']
                    ?? 'Tidak Diketahui';

                $kotaKabupaten = trim(str_ireplace('Kabupaten', '', $kotaKabupaten));

                // Backup bertingkat untuk Kecamatan/Kelurahan/Desa
                $kecamatan = $address['village']
                    ?? $address['suburb']
                    ?? $address['neighbourhood']
                    ?? $address['city_district']
                    ?? 'Tidak Diketahui';

                return response()->json([
                    'status' => 'success',
                    'kota_kabupaten' => $kotaKabupaten,
                    'kecamatan' => $kecamatan
                ]);
            }
        }

        // Response backup jika API OpenStreetMap gagal dihubungi atau data tidak sesuai
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data lokasi atau format alamat tidak dikenali'
        ], 500);
    }
}
