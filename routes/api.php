<?php

use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;

Route::post('/sensor-data', [SensorController::class, 'store']);