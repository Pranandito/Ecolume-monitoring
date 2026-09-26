<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveFolder;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use stdClass;

class StorageController extends Controller
{
    public function __construct(protected GoogleDriveService $drive) {}

    public function getNgrokConfig(): ?array
    {
        try {
            $response = Http::timeout(3)->get('http://127.0.0.1:4040/api/tunnels');

            if (!$response->successful()) {
                return null;
            }

            $tunnels = $response->json('tunnels', []);

            // Cari tunnel TCP (bisa lebih spesifik lagi kalau ada banyak tunnel aktif,
            // misal filter berdasarkan config.addr yang mengarah ke port 1883)
            $tcpTunnel = collect($tunnels)->first(function ($tunnel) {
                return $tunnel['proto'] === 'tcp'
                    && str_contains($tunnel['config']['addr'] ?? '', '1883');
            });

            if (!$tcpTunnel) {
                return null;
            }

            // public_url formatnya: tcp://0.tcp.ap.ngrok.io:12345
            $parsed = parse_url($tcpTunnel['public_url']);

            return [
                'host' => $parsed['host'],
                'port' => $parsed['port'],
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getMySqlSize()
    {
        $dbName = config('database.connections.mysql.database');

        $result = DB::selectOne("
            SELECT 
                SUM(data_length + index_length) AS size_bytes
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$dbName]);

        return $result->size_bytes ?? 0;
    }

    public function getBucketSize(?string $bucketName = null)
    {
        $host   = config('services.influxdb.host');
        $port   = config('services.influxdb.port');
        $ssl    = filter_var(config('services.influxdb.ssl'), FILTER_VALIDATE_BOOLEAN);
        $scheme = $ssl ? 'https' : 'http';

        $baseUrl = "{$scheme}://{$host}:{$port}";
        $token   = config('services.influxdb.token');
        $org     = config('services.influxdb.org');
        $bucketName = $bucketName ?? config('services.influxdb.bucket');

        $headers = ['Authorization' => "Token {$token}"];

        $bucketResp = Http::withHeaders($headers)
            ->timeout(config('services.influxdb.timeout', 5))
            ->get("{$baseUrl}/api/v2/buckets", [
                'org'  => $org,
                'name' => $bucketName,
            ]);

        $bucketId = data_get($bucketResp->json(), 'buckets.0.id');

        if (!$bucketId) {
            return response()->json([
                'error'         => 'Bucket tidak ditemukan',
                'debug_status'  => $bucketResp->status(),
                'debug_body'    => $bucketResp->json(),
            ], 404);
        }

        $metricsText = Http::withHeaders($headers)->get("{$baseUrl}/metrics")->body();

        $totalBytes = 0;
        foreach (explode("\n", $metricsText) as $line) {
            if (str_starts_with($line, 'storage_shard_disk_size{') && str_contains($line, "bucket=\"{$bucketId}\"")) {
                $parts = preg_split('/\s+/', trim($line));
                $totalBytes += (float) end($parts);
            }
        }

        return $totalBytes;
    }

    private function getDirectorySize(string $path): int
    {
        $bytes = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $bytes += $file->getSize();
            }
        }

        return $bytes;
    }

    public function brokerStatus($host, $port)
    {
        // $host = env('MQTT_HOST'); // host dari ngrok
        // $port = env('MQTT_PORT'); // port dari ngrok

        $clientId = 'status-check-' . uniqid();
        $mqtt = new MqttClient($host, $port, $clientId);

        $settings = (new ConnectionSettings())
            ->setConnectTimeout(3)
            ->setKeepAliveInterval(10);

        try {
            $mqtt->connect($settings, true);
            $mqtt->disconnect();

            return "Online";
        } catch (\Exception $e) {
            return "Offline";
        }
    }

    public function index()
    {
        // Cuma data yang murah/cepat → langsung dari DB, tanpa HTTP call keluar
        $users = new stdClass();
        $users_data = User::where('role', 'user')->select('id', 'role', 'last_seen')->get();
        $users->activeToday    = $users_data->filter(fn($u) => $u->last_seen?->isToday())->count();
        $users->activeThisWeek = $users_data->filter(fn($u) => $u->last_seen?->isSameWeek(now()))->count();
        $users->count          = $users_data->count();

        $folders = ArchiveFolder::get();

        // View di-render TANPA menunggu disk size, influxdb, ngrok, mqtt
        return view('admin.storage', compact('users', 'folders'));
    }

    public function storageMetrics()
    {
        $size = new stdClass();

        $totalDisk = disk_total_space("/") - 20000000000;
        $freeDisk  = disk_free_space("/");
        $usedDisk  = $totalDisk - $freeDisk;

        $size->totalDisk   = round($totalDisk / 1024 / 1024 / 1024, 2);
        $size->freeDisk    = round($freeDisk / 1024 / 1024 / 1024, 2);
        $size->usedDisk    = round($usedDisk / 1024 / 1024 / 1024, 2);
        $size->usedDiskPct = round($usedDisk / $totalDisk * 100);

        $other = $this->getDirectorySize(base_path());
        $sql   = $this->getMySqlSize();
        $influxdb = $this->getBucketSize("PTSP_Raw") + $this->getBucketSize("PTSP_AllTime");

        $size->other    = round($other / 1024 / 1024 / 1024, 2);
        $size->sql      = round($sql / 1024 / 1024 / 1024, 2);
        $size->influxdb = round($influxdb / 1024 / 1024 / 1024, 2);

        $size->otherPct    = max(1, ceil($other / $totalDisk * 100));
        $size->sqlPct      = max(1, ceil($sql / $totalDisk * 100));
        $size->influxdbPct = max(1, ceil($influxdb / $totalDisk * 100));

        $project = $other + $sql + $influxdb;
        $size->project    = round($project / 1024 / 1024 / 1024, 2);
        $size->projectPct = max(1, ceil($project / $totalDisk * 100));

        $otherProject = $usedDisk - $project;
        $size->otherProject    = round($otherProject / 1024 / 1024 / 1024, 2);
        $size->otherProjectPct = max(1, ceil($otherProject / $totalDisk * 100));

        $size->status = match (true) {
            $size->usedDiskPct > 90 => 'Penuh',
            $size->usedDiskPct > 75 => 'Terbatas',
            default => 'Aman',
        };

        return response()->json(compact('size'));
    }

    public function mqttMetrics()
    {
        $mqtt = new stdClass();
        $ngrok = $this->getNgrokConfig();
        if (!$ngrok) {
            $mqtt->status = "Offline";
            $mqtt->ngrokPort = "-";
        } else {
            $mqtt->status = $this->brokerStatus($ngrok['host'], $ngrok['port']);
            $mqtt->ngrokPort = $ngrok['port'];
        }

        return response()->json(compact('mqtt', 'ngrok'));
    }
}
