<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [TripController::class, 'index'])->name('dashboard');
    Route::get('trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('trips/{trip}', [TripController::class, 'show'])->name('trips.show');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
