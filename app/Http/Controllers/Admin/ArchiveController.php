<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveFolder;
use App\Services\GoogleDriveService;
use App\Services\Logger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ArchiveController extends Controller
{
    public function __construct(
        protected GoogleDriveService $drive,
        protected Logger $logger
    ) {}

    public function create(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'required|string',
            // 'owner' => 'required|string'
        ]);

        if (ArchiveFolder::where('folder_id', $validated['folder_id'])->exists()) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menambahkan folder. Pastikan folder yang dimasukkan tidak berulang'
            ]);
        }

        try {
            $drive = $this->drive->getFolderInfo($validated['folder_id']);
        } catch (\Exception $e) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menambahkan folder. Pastikan id folder yang dimasukkan sudah benar'
            ]);
        }
        $permision = array_find($drive->getPermissions(), function ($permision) {
            return $permision['emailAddress'] == "ecolume-laravel-gdrive-bot@avid-folder-448602-f0.iam.gserviceaccount.com";
        });

        if (!$permision) {
            return back()->with([
                'status' => 'error',
                'title' => 'Terjadi Kesalahan',
                'desc' => 'Gagal menambahkan folder. Pastikan akses email sudah diberikan'
            ]);
        }

        $validated["owner"] = $drive->getOwners()[0]->emailAddress;
        $validated["name"] = $drive->name;
        $validated["permision"] = $permision->role ?? "viewer";

        $create = ArchiveFolder::create($validated);

        $data = [
            "folder_name" => $drive->name,
            "admin_name" => Auth::user()->name
        ];

        $this->logger->create(Auth::id(), "addArchiveFolder", "folders", $create->id, $data, null);

        return back()->with([
            'status' => 'success',
            'title' => 'Berhasil Ditambahkan!🎉',
            'desc' => 'Berhasil menambahkan folder. Pastikan akun google drive anda memiliki ruang penyimpanan minimal 20GB'
        ]);
    }

    public function delete(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return 'tidak punya akses admin';
        }

        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $deviceExist = ArchiveFolder::where('id', $validated['id'])->first();

        if (!$deviceExist) {
            return "archive tidak ada di sistem";
        }

        $delete = $deviceExist->delete();

        return redirect()->back();
    }
}
