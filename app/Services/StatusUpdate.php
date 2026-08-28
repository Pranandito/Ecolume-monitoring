<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceConfig;
use Carbon\Carbon;

class StatusUpdate
{
    public function DeviceStatusUpdate($device_id, $daya, $debit, $tegangan)
    {
        $operationalStatus = 'Off';

        $dayaThreshold = 5;       // Watt
        $debitThreshold = 0.1;    // L/min
        $teganganMinimum = 10;     // Volt, hanya untuk mendeteksi supply

        $teganganAda = $tegangan > $teganganMinimum;
        $dayaAktif = $daya > $dayaThreshold;
        $debitAktif = $debit > $debitThreshold;

        if (!$teganganAda) {
            // Tidak ada supply listrik
            $operationalStatus = 'Off';
        } elseif (!$dayaAktif && !$debitAktif) {
            // Supply ada, tetapi pompa tidak bekerja
            $operationalStatus = 'Off';
        } elseif ($dayaAktif && $debitAktif) {
            // Pompa mengonsumsi daya dan menghasilkan debit
            $operationalStatus = 'On';
        } else {
            // Kondisi tidak sesuai
            $operationalStatus = 'Anomaly';
        }

        $update = Device::where('id', $device_id)->update([
            "online_status" => 1,
            "last_seen" => now(),
            "operational_status" => $operationalStatus
        ]);
    }

    public function DeviceOnlineStatusUpdate($device_id)
    {
        $update = Device::where('id', $device_id)->update([
            "online_status" => 1,
        ]);
    }
}
