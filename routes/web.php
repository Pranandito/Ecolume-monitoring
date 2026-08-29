<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\CarbonController;
use App\Http\Controllers\CuacaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceModeController;
use App\Http\Controllers\EspController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('beranda');
});

// Route::get('/dashboard/line-chart/{device_id}', [DashboardController::class, 'getChartData'])->name('dashboard.line-chart');

Route::middleware('auth')->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->middleware(['auth', 'verified'])->name('beranda');
    Route::get('/ramalan-cuaca/{device_name}/{device_id}', [CuacaController::class, 'index'])->middleware(['auth', 'verified'])->name('ramalan-cuaca');
    Route::get('/dashboard/{device_name?}/{device_id?}', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::patch('/carbon-factor/update/{id}', [CarbonController::class, 'update'])->name('carbonFactor.update');

    Route::patch('/device/rename', [DeviceController::class, 'deviceRename'])->name('device.rename');
    Route::patch('/device/claim', [DeviceController::class, 'deviceClaim'])->name('device.claim');
    Route::patch('/device/remove', [DeviceController::class, 'deviceRemove'])->name('device.remove');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/rename', [ProfileController::class, 'rename'])->name('profile.rename');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/device/tes', [EspController::class, 'tes2']);

Route::get('/device/test/{device_id}', [DashboardController::class, 'getHeatMapData']);
Route::get('/device/line-chart/{device_id}', [DashboardController::class, 'getChartData'])->name('dashboard.line-chart');
Route::get('/device/{device_id}/kinerja-baseline', [DashboardController::class, 'kinerjaBaseline'])->name('kinerja.baseline');
Route::get('/device/{device_id}/session-baseline', [DashboardController::class, 'sessionBaseline'])->name('device.session-baseline');

Route::post('/device/mode/{device_id}/{serial_number}', [DeviceModeController::class, 'update'])->name('device.mode');


Route::middleware('auth', 'admin')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/add', [AdminController::class, 'addAdmin'])->name('admin.add');

    Route::delete('/device/delete', [DeviceController::class, 'delete'])->name('device.delete');
    Route::post('/device/create', [DeviceController::class, 'create'])->name('device.create');
});



require __DIR__ . '/auth.php';
