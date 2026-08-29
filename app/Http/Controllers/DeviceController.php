<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceConfig;
use App\Services\Logger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function __construct(
        private Logger $log
    ) {}

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

        $data = [
            "admin_name" => Auth::user()->name,
            "device_id" => $device->id
        ];

        $this->log->create(Auth::id(), "createDevice", "Device", $device->id, $data);

        return redirect()->route('admin.dashboard');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|integer'
        ]);

        $deviceExist = Device::where('id', $validated['device_id'])->first();

        if (!$deviceExist) {
            return "device tidak ada di sistem";
        }

        if (Auth::user()->role !== 'admin') {
            return 'tidak punya akses admin';
        }

        $data_record = $deviceExist->toArray();
        $delete = $deviceExist->delete();

        $data = [
            "admin_name" => Auth::user()->name,
            "device_id" => $deviceExist->id
        ];

        $this->log->create(Auth::id(), 'deleteDevice', 'device', $deviceExist->id, $data, $data_record);

        return redirect()->back();
    }

    public function deviceRename(Request $request)
    {
        $validated = $request->validate([
            'device_id'      => ['required', 'exists:devices,id'],
            'new_devicename'      => ['required', 'string', 'max:15'],
        ]);

        $device = Device::where('id', $validated['device_id'])
            ->first();

        if (!$device) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal mengubah nama perangkat. Perangkat tidak ditemukan'
            ]);
        }

        if (!is_null($device->owner_id) && $device->owner_id !== Auth::id()) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal mengubah nama perangkat. Perangkat telah terhubung dengan akun lain'
            ]);
        }

        $device->update([
            'device_name' => $validated['new_devicename'],
        ]);

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Diganti!🎉',
            'desc' => 'Perubahan nama menjadi ' . $device->name . ' telah berhasil',
        ]);
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

        $data = [
            "username" => Auth::user()->name,
            "device_serial_number" => $device->serial_number
        ];

        $this->log->create(Auth::id(), 'claimDevice', 'device', $device->id, $data);

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Ditambahkan!🎉',
            'desc' => 'Perangkat ' . $deviceName . ' telah berhasil ditambahkan ke dalam sistem Anda',
        ]);
    }

    public function deviceRemove(Request $request)
    {
        $validated = $request->validate([
            'device_id'      => ['required', 'exists:devices,id'],
        ]);

        $device = Device::where('id', $validated['device_id'])
            ->first();

        if (!$device) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menghapus perangkat. Perangkat tidak ditemukan'
            ]);
        }

        if (!is_null($device->owner_id) && $device->owner_id !== Auth::id()) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menghapus perangkat. Perangkat telah terhubung dengan akun lain'
            ]);
        }

        $device->update([
            'owner_id' => null,
            'device_name' => 'Portable-PTS',
            'claim_at' => null
        ]);

        $data = [
            "username" => Auth::user()->name,
            "device_serial_number" => $device->serial_number
        ];

        $this->log->create(Auth::id(), 'removeDevice', 'device', $device->id, $data);

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Dihapus!🎉',
            'desc' => 'Perangkat ' . $device->name . ' telah berhasil dihapus ke dalam sistem Anda',
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
