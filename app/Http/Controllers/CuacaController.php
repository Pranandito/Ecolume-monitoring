<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuacaController extends Controller
{
    public function index($device_name, $device_id)
    {
        $dev_id = $device_id;
        $dev_name = $device_name;
        $devices = Device::select('id', 'owner_id', 'serial_number', 'device_name', 'online_status')->where('owner_id', Auth::id())->with('device_config')->get();
        $device = $devices->firstWhere('id', $dev_id);

        return view('cuaca', compact('devices', 'device', 'dev_id', 'dev_name'));
    }
}
