<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrashBinController;
use App\Http\Controllers\SensorLogController;

Route::apiResource('trash-bins', TrashBinController::class);
Route::get('sensor-logs/{trashBinId}', [SensorLogController::class, 'index']);
Route::post('sensor-logs', [SensorLogController::class, 'store']);