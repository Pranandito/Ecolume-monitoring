<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::select('id', 'last_seen', 'online_status', 'owner_id', 'device_name')->with(['device_config' => function ($querry) {
            $querry->select('id', 'device_id', 'lat', 'long', 'location');
        }])->get();

        $count = new stdClass;
        $count->online = $devices->where('online_status', 1)->count();
        $count->offline = $devices->count() - $count->online;
        $count->onlineToday = $devices->filter(fn($device) =>
        $device->last_seen !== null && Carbon::parse($device->last_seen)
            ->isToday())->count();

        $devices = $devices->whereNotNull('owner_id');

        $adminList = User::where('role', 'admin')->select('id', 'name', 'email', 'role', 'role_updated_at')->get();

        $deviceList = Device::with(['user' => function ($querry) {
            $querry->select('id', 'name');
        }])->paginate(5);


        $activityLogs = ActivityLog::select('id', 'created_at', 'title', 'desc')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('devices', 'count', 'adminList', 'deviceList', 'activityLogs'));
    }
}
