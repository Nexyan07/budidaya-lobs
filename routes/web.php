<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PopulationController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/show-histories', [DashboardController::class, 'showHistories'])->middleware(['auth', 'verified']);

Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/control', [DeviceController::class, 'index'])->name('control');
    Route::post('/control', [DeviceController::class, 'updateBulk'])->name('devices.updateBulk');

    Route::get("/export-histories", [DashboardController::class, 'export']);

    Route::resource('populations', PopulationController::class)->only(['store', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
