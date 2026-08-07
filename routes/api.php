<?php

use App\Http\Controllers\EspController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/mqtt-port/{device_id}', [
        EspController::class,
        'getTunellingPort'
    ]);
});
