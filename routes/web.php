<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\CarbonController;
use App\Http\Controllers\CuacaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceModeController;
use App\Http\Controllers\MQTTController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/beranda', [BerandaController::class, 'index'])->middleware(['auth', 'verified'])->name('beranda');
Route::get('/dashboard/{device_name}/{device_id}', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/ramalan-cuaca/{device_name}/{device_id}', [CuacaController::class, 'index'])->middleware(['auth', 'verified'])->name('ramalan-cuaca');

// Route::get('/dashboard/line-chart/{device_id}', [DashboardController::class, 'getChartData'])->name('dashboard.line-chart');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::patch('/carbon-factor/update/{id}', [CarbonController::class, 'update'])->name('carbonFactor.update');

Route::post('/device/create', [DeviceController::class, 'create'])->name('device.create');
Route::patch('/device/update/{id}', [DeviceController::class, 'update'])->name('device.update');


Route::get('/device/tes', [BerandaController::class, 'test']);
Route::get('/device/test/{device_id}', [DashboardController::class, 'getHeatMapData']);
Route::get('/device/line-chart/{device_id}', [DashboardController::class, 'getChartData'])->name('dashboard.line-chart');
Route::get('/device/{device_id}/kinerja-baseline', [DashboardController::class, 'kinerjaBaseline'])->name('kinerja.baseline');
Route::get('/device/{device_id}/session-baseline', [DashboardController::class, 'sessionBaseline'])->name('device.session-baseline');

Route::POST('/device/mode/{device_id}/{serial_number}', [DeviceModeController::class, 'update'])->name('device.mode');

require __DIR__ . '/auth.php';
