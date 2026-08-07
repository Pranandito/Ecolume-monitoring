<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;
use RikoDEV\InfluxDB\Facades\InfluxDB;


class BerandaController extends Controller
{
    public function getDeviceLatest($device_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_Raw")
        |> range(start: 0)
        |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
        |> filter(fn: (r) => r["Device_Id"] == "$device_id")
        |> last()
        |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")
        FLUX;

        $result = $queryApi->query($fluxQuery);

        $data = [];

        foreach ($result as $table) {
            foreach ($table->records as $record) {
                $data = $record->values;
            }
        }

        return $data;
    }

    function getDailyLogs($devices_id)
    {
        $queryApi = InfluxDB::createQueryApi();

        // Pastikan $devices_id diformat dengan benar menjadi string array Flux, misalnya: '["DevA", "DevB"]'
        $fluxQuery = <<<FLUX
        from(bucket: "PTSP_AllTime")
            |> range(start: 0)
            |> filter(fn: (r) => r["_measurement"] == "IoT_PTSP")
            |> filter(fn: (r) => contains(value: r["Device_Id"], set: $devices_id))
            |> pivot(rowKey: ["_time"], columnKey: ["_field"], valueColumn: "_value")
            
            // KUNCI PERBAIKAN: Fungsi group() tanpa parameter akan menghapus grouping berdasarkan tags
            // sehingga semua data melebur menjadi satu tabel besar.
            |> group() 
            
            // Setelah dilebur, baru kita urutkan secara global berdasarkan waktu terbaru
            |> sort(columns: ["_time"], desc: true)
            
            // Lalu ambil 8 baris pertama (yang mana adalah 8 data paling baru secara keseluruhan)
            |> limit(n: 8)
            
            |> keep(columns: [
                "_time", 
                "Device_Id", 
                "Volume_delta", 
                "Durasi_Operasional_delta", 
                "Energi_delta"
            ])
        FLUX;

        $result = $queryApi->query($fluxQuery);

        $data = [];

        foreach ($result as $table) {
            foreach ($table->records as $record) {
                // $record->values secara otomatis mengembalikan associative array
                // berisi semua kolom yang kamu pertahankan di fungsi keep()
                $data[] = $record->values;
            }
        }

        return $data;
    }

    public function test()
    {
        $id = 1;
        $device = Device::where('id', $id)->select('id', 'API_keys')->with('device_config')->first();
        $device_config = $device->device_config;
        return $device_config->id;
    }

    public function index()
    {
        $devices = Device::select('id', 'owner_id', 'serial_number', 'device_name', 'online_status')->where('owner_id', Auth::id())->get();

        $deviceData = [];
        foreach ($devices as $device) {
            $latestData = $this->getDeviceLatest($device->id);
            $deviceData[$device->id] = [
                "Daya" => $latestData["Daya"] ?? 0,
                "Debit" => $latestData["Debit"] ?? 0,
                "Time" => !empty($latestData["_time"])
                    ? \Carbon\Carbon::parse($latestData["_time"], 'UTC')
                    ->timezone('Asia/Jakarta')
                    ->locale('id')
                    ->translatedFormat('d F H:i')
                    : '-',
            ];
        }

        $deviceIds = $devices->pluck('id')->toArray();
        $devicesId = '["' . implode('","', $deviceIds) . '"]';
        $deviceLogs = $this->getDailyLogs($devicesId);

        $logData = [];
        foreach ($devices as $device) {
            $logData[$device->id] = $device->device_name;
        };

        return view('beranda', compact('devices', 'deviceData', 'deviceLogs', 'logData'));
    }
}
