<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MQTTController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/beranda', [BerandaController::class, 'index'])->middleware(['auth', 'verified'])->name('beranda');
Route::get('/dashboard/{device_name}/{device_id}', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
// Route::get('/dashboard/line-chart/{device_id}', [DashboardController::class, 'getChartData'])->name('dashboard.line-chart');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/device/create', [DeviceController::class, 'create'])->name('device.create');
Route::patch('/device/update/{id}', [DeviceController::class, 'update'])->name('device.update');


Route::get('/device/tes', [BerandaController::class, 'test']);
Route::get('/device/test/{device_id}', [DashboardController::class, 'tes']);
Route::get('/device/line-chart/{device_id}', [DashboardController::class, 'getChartData'])->name('dashboard.line-chart');




require __DIR__ . '/auth.php';
