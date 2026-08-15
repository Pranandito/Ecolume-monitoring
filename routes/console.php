<?php

use App\Console\Commands\CheckDeviceOnlineStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckDeviceOnlineStatus::class)
    ->everyFiveMinutes()
    ->withoutOverlapping(10) // lock 10 menit, jaga-jaga kalau query lambat
    // ->onOneServer() // penting kalau nanti scale ke >1 server
    ->appendOutputTo(storage_path('logs/device-status-check.log'));
