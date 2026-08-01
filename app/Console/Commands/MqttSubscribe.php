<?php

namespace App\Console\Commands;

use App\Services\MQTTStoreService;
use App\Services\JobConfirm as ServicesJobConfirm;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class MqttSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to all MQTT topics (Data & Job Confirm) and save to database';

    /**
     * Execute the console command.
     */
    public function handle(ServicesJobConfirm $jobService, MQTTStoreService $storeService)
    {
        $this->info('Subscribing to MQTT topics...');

        // Ambil koneksi client
        $mqtt = MQTT::connection('subscriber');
        $mqtt->connect();

        // Subscribe ke wildcard utama (ecolumeIoT/#)
        $mqtt->subscribe('ecolumeIoT/#', function (string $topic, string $message) use ($jobService, $storeService) {
            $topics = explode('/', $topic);

            // 1. Logika untuk Job Confirm (ecolumeIoT/jobConfirm/{serial_number}/{id})
            if (isset($topics[1]) && $topics[1] === 'jobConfirm') {
                $serial_number = $topics[2] ?? null;
                $id = $topics[3] ?? null;

                if ($id) {
                    $updateStatus = $jobService->JobConfirm($message, $id, $serial_number);
                    if ($updateStatus) {
                        $this->info("[JobConfirm] Berhasil disimpan | Device id : {$id}");
                    } else {
                        $this->error("[JobConfirm] Job usang | Device id : {$id}");
                    }
                }

                // Hentikan eksekusi di sini agar tidak lanjut ke penyimpanan data sensor
                return;
            }

            // 2. Logika untuk Data Sensor (ecolumeIoT/{serial_number}/{id})
            $serial_number = $topics[1] ?? null;
            $id = $topics[2] ?? null;

            if ($serial_number && $id) {
                [$savingStatus, $dbLoc] = $storeService->MQTTStore($message, $serial_number, $id);

                if ($savingStatus) {
                    $this->info("[SensorData] Berhasil disimpan | Lokasi : {$dbLoc} | " . date('Y-m-d H:i:s'));
                } else {
                    $this->error("[SensorData] Gagal menyimpan data | Device id : {$id}");
                }
            }
        }, 1);

        $this->info('Listening... (press Ctrl+C to stop)');

        // Loop terus — blocking
        $mqtt->loop(true);

        // Disconnect saat loop selesai (Ctrl+C)
        $mqtt->disconnect();
    }
}
