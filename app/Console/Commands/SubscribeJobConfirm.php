<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobConfirm;
use PhpMqtt\Client\Facades\MQTT;

class SubscribeJobConfirm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe-jobConfirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe Konfirmasi Job ESP32';

    /**
     * Execute the console command.
     */
    public function handle(JobConfirm $service)
    {
        $this->info('Subscribing to MQTT topics...');

        // Ambil koneksi client dulu
        $mqtt = MQTT::connection();
        $mqtt->connect();

        // Subscribe ke topic
        $mqtt->subscribe('ecolumeIoT/jobConfirm/#', function (string $topic, string $message) use ($service) {
            $topics = explode('/', $topic);
            $serial_number = $topics[2];
            $id = $topics[3];
            $updateStatus = $service->JobConfirm($message, $id, $serial_number);

            if ($updateStatus) {
                $this->info("Berhasil disimpan");
                $this->info("Device id : {$id}");
            } else {
                $this->error("Job usang");
            }
        }, 1);

        $this->info('Listening... (press Ctrl+C to stop)');

        // Loop terus — blocking
        $mqtt->loop(true);

        // Disconnect saat loop selesai (Ctrl+C)
        $mqtt->disconnect();
    }
}
