<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Firmware;
use App\Services\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpMqtt\Client\Facades\MQTT;

class OTAController extends Controller
{
    public function __construct(
        protected Logger $logger
    ) {}

    public function create(Request $request)
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
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menambahkan arsip firmware. Pastikan tidak ada duplikasi versi firmware'
            ]);
        }

        $file = $request->file('firmware');
        $file_name = "firmware_v" . $validated['firmware_ver'] . ".bin";
        $validated['file_path'] = $file->storeAs('firmwares', $file_name, 'public');

        $firmware_codes = explode('.', $validated['firmware_ver']);
        $validated['firmware_code'] = $firmware_codes[0] * 1000000  + $firmware_codes[1] * 1000 + $firmware_codes[2] * 1;
        $validated['size'] = $file->getSize();

        $admin = Auth::user()->name;
        $validated['uploader'] = $admin;

        unset($validated['firmware']);
        $create = Firmware::create($validated);

        $data = [
            "admin_name" => $admin,
            "file_name" => $file_name
        ];

        $this->logger->create(Auth::id(), "addFirmware", "firmwares", $create->id, $data, null);

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Ditambahkan!🎉',
            'desc' => 'Berhasil menambahkan arsip firmware. Broadcast flash fimrware ke hardware jika sudah siap'
        ]);
    }

    public function download()
    {
        $fw = Firmware::where('is_prod', 1)->first();

        abort_unless(Storage::disk('public')->exists($fw->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($fw->file_path),
            basename($fw->file_path),
            ['Content-Type' => 'application/octet-stream']
        );
    }

    public function broadcast(Request $request, $id)
    {
        $firmware = Firmware::findOrFail($id);

        $request->validate([
            'confirm_version' => ['required', 'in:' . $firmware->firmware_ver],
        ], [
            'confirm_version.in' => 'Versi yang diketik tidak sesuai.',
        ]);

        // Ganti firmware produksi secara atomik: hanya satu yang is_prod = 1
        DB::transaction(function () use ($firmware) {
            Firmware::where('is_prod', 1)
                ->where('id', '!=', $firmware->id)
                ->update(['is_prod' => 0]);

            if (! $firmware->is_prod) {
                $firmware->update(['is_prod' => 1]);
            }
        });

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Dipublish!🎉',
            'desc' => "Berhasil mempublish firmware versi {$firmware->firmware_ver}. Tunggu beberapa saat untuk hardware mengupdate firmware.",
        ]);
    }

    public function active()
    {
        $active = Firmware::where('is_prod', 1)->first();

        return response()->json([
            "version_code" => $active->firmware_code,
        ]);
    }

    public function index()
    {
        $firmwares = Firmware::orderBy('firmware_code', 'desc')->paginate(5);
        foreach ($firmwares as $firmware) {
            $fw = explode('/', $firmware->file_path);
            $firmware->file_name = end($fw);
        }

        return view('admin.ota', compact('firmwares'));
    }
}
