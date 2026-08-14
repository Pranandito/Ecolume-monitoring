<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceConfig;
use Carbon\Carbon;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

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

    public function deviceClaim(Request $request)
    {
        $validated = $request->validate([
            'owner_id'      => ['required', 'exists:users,id'],
            'serial_number' => ['required', 'string', 'max:15'],
        ]);

        $device = Device::where('serial_number', $validated['serial_number'])
            ->first();

        if (!$device) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menambahkan perangkat. Pastikan serial number yang Anda masukkan sudah benar'
            ]);
        }

        if (!is_null($device->owner_id)) {
            if ($device->owner_id == Auth::id()) {
                return back()->with([
                    'status' => 'error',
                    'title' => 'Terjadi Kesalahan',
                    'desc' => 'Gagal menambahkan perangkat. Perangkat sudah terhubung ke sistem Anda'
                ]);
            }

            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menambahkan perangkat. Perangkat telah terhubung dengan akun lain'
            ]);
        }

        $deviceCount = Device::where('owner_id', Auth::id())->count();
        if ($deviceCount == 0) {
            $deviceCount = "";
        }
        $deviceName = 'Portable-PTS-' . $deviceCount + 1;

        $device->update([
            'owner_id' => $validated['owner_id'],
            'device_name' => $deviceName,
            'claim_at' => Carbon::now()
        ]);

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Ditambahkan!🎉',
            'desc' => 'Perangkat ' . $deviceName . ' telah berhasil ditambahkan ke dalam sistem Anda',
        ]);
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
