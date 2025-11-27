<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\JournalEntryController;
use App\Http\Controllers\Api\TripController as ApiTripController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthTokenController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());

    Route::apiResource('trips', ApiTripController::class);
    Route::get('trips/{trip}/entries', [JournalEntryController::class, 'index'])->name('trips.entries.index');
    Route::post('trips/{trip}/entries', [JournalEntryController::class, 'store'])->name('trips.entries.store');
    Route::get('trips/{trip}/weather', [WeatherController::class, 'index'])->name('trips.weather.index');
    Route::post('trips/{trip}/weather/fetch', [WeatherController::class, 'fetch'])->name('trips.weather.fetch');
});
