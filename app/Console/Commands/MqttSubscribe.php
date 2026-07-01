<?php

namespace App\Console\Commands;

use App\Services\MQTTStoreService;
use App\Models\SensorData;
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
    protected $description = 'Subscribe to MQTT topics and save to database';

    /**
     * Execute the console command.
     */
    public function handle(MQTTStoreService $service)
    {
        $this->info('Subscribing to MQTT topics...');

        // Ambil koneksi client dulu
        $mqtt = MQTT::connection();
        $mqtt->connect();

        // Subscribe ke topic
        $mqtt->subscribe('ecolumeIoT/#', function (string $topic, string $message) use ($service) {
            $topics = explode('/', $topic);
            $serial_number = $topics[1];
            $id = $topics[2];
            $savingStatus = $service->MQTTStore($message, $serial_number, $id);

            // 4. Pindahkan output info ke DALAM sini
            // Panggil spesifik properti misalnya $save->id, atau ubah jadi json
            if ($savingStatus) {
                $this->info("Berhasil disimpan");
                $this->info(date('Y-m-d H:i:s', time()));
            } else {
                $this->error("Gagal menyimpan data");
            }
        }, 1);

        $this->info('Listening... (press Ctrl+C to stop)');

        // Loop terus — blocking
        $mqtt->loop(true);

        // Disconnect saat loop selesai (Ctrl+C)
        $mqtt->disconnect();
    }
}
