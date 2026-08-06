<?php

namespace App\Http\Controllers;

use App\Models\DeviceConfig;
use App\Services\MQTTPublish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RikoDEV\InfluxDB\Facades\InfluxDB;
use Illuminate\Support\Str;
use PhpMqtt\Client\Facades\MQTT;

class DeviceModeController extends Controller
{
    public function __construct(
        protected MQTTPublish $mqttPublish
    ) {}

    public function update(Request $request, string $device_id, string $serial_number,)
    {
        $validated = $request->validate([
            'mode'         => ['required', Rule::in(['On', 'Off', 'Timer Volume', 'Timer Waktu'])],
            'timer_start'  => ['required_if:mode,Timer Waktu', 'nullable', 'date_format:H:i'],
            'timer_end'    => ['required_if:mode,Timer Waktu', 'nullable', 'date_format:H:i', 'after:timer_start'],
            'volume_limit' => ['required_if:mode,Timer Volume', 'nullable', 'integer', 'min:1'],
        ]);

        $config = DeviceConfig::where('device_id', $device_id)->firstOrFail();

        $config->mode = $validated['mode'];
        $config->timer_start  = null;
        $config->timer_end    = null;
        $config->volume_limit = null;
        $config->volume_progress = 0;
        $config->job_confirmed = 0;
        $config->job_id = ($config->job_id ?? 0) + 1;
        $config->job_created = now();

        if ($validated['mode'] === 'Timer Waktu') {
            $config->timer_start = $validated['timer_start'];
            $config->timer_end   = $validated['timer_end'];
        }
        if ($validated['mode'] === 'Timer Volume') {
            $config->volume_limit = $validated['volume_limit'];
        }
        $config->save();

        $this->mqttPublish->publishMode($device_id, $serial_number, $config, 1);

        $tegangan = $this->getLatestTegangan($device_id);

        return response()->json([
            'success'   => true,
            'data'      => $config,
            'device_id' => $device_id,
            'html' => $this->renderModeCard($config, ['Tegangan' => $tegangan]),
        ]);
    }

    private function getLatestTegangan($device_id)
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

        return $data["Tegangan"];
    }

    private function renderModeCard(DeviceConfig $config, array $latest = []): string
    {
        $componentView = 'components.mode.' . Str::of($config->mode)->kebab();

        return view($componentView, [
            'device_config' => $config,
            'tegangan'      => $latest['Tegangan'] ?? null, // dipakai hanya oleh mode.off, diabaikan komponen lain
        ])->render();
    }
}
