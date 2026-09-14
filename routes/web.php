<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VehicleAvailabilityController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/kendaraan', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/kendaraan/{vehicle}', [VehicleController::class, 'show'])
    ->name('vehicles.show')
    ->where('vehicle', '[0-9]+');

// JSON endpoint for the availability calendar (read-only)
Route::get('/api/kendaraan/{vehicle}/ketersediaan', [VehicleAvailabilityController::class, 'show'])
    ->name('vehicles.availability');

Route::get('/tentang', [PageController::class, 'about'])->name('pages.about');
Route::get('/kontak', [PageController::class, 'contact'])->name('pages.contact');
