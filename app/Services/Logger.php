<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\DeviceConfig;
use App\Services\MQTTPublish;
use App\Services\StatusUpdate;


class Logger
{
    public function create($account_id = null, $action, $subject_type, $subject_id, $data, $data_record = null)
    {
        // action = addDevice, newUser, deleteDevice, deviceClaim, addAdmin
        $titleMap = [
            "createDevice" => "Menambahkan Device",
            "deleteDevice" => "Menghapus Device",
            "newUser" => "Akun Baru Terdaftar",
            "claimDevice" => "Device Terhubung ke User",
            "removeDevice" => "Device Dilepas User",
            "addAdmin" => "Penambahan Admin"
        ];

        $descMap = [
            "createDevice" => fn() => "{$data['admin_name']} menambahkan device - ID: {$data['device_id']}",
            "deleteDevice" => fn() => "{$data['admin_name']} menghapus device dari sistem - ID: {$data['device_id']}",
            "newUser" => fn() => "Akun baru atas nama {$data['new_username']} telah dibuat",
            "claimDevice" => fn() => "{$data['device_serial_number']} dihubungkan ke akun {$data['username']}",
            "removeDevice" => fn() => "{$data['username']} melepas perangkat {$data['device_serial_number']}",
            "addAdmin" => fn() => "{$data['added_admin_name']} ditingkatkan menjadi admin oleh {$data['admin_name']}",
        ];

        $logging = ActivityLog::create([
            'account_id' => $account_id, // id akun admin yang melakukan
            'action' => $action,
            'subject_type' => $subject_type, // user, admin, device
            'subject_id' => $subject_id,
            'title' => $titleMap[$action], // kebutuhan fe
            'desc' => $descMap[$action](), // kebutuhan fe
            'data_record' => $data_record, // ketika delete
        ]);
    }
}
