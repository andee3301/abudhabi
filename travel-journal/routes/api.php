<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\ItineraryItemController;
use App\Http\Controllers\Api\JournalEntryController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TripController as ApiTripController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthTokenController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());

    Route::get('/trips', [ApiTripController::class, 'index']);
    Route::post('/trips', [ApiTripController::class, 'store']);
    Route::get('/trips/{trip}', [ApiTripController::class, 'show']);
    Route::put('/trips/{trip}', [ApiTripController::class, 'update']);
    Route::delete('/trips/{trip}', [ApiTripController::class, 'destroy']);

    Route::get('/trips/{trip}/itinerary', [ItineraryItemController::class, 'index']);
    Route::post('/trips/{trip}/itinerary', [ItineraryItemController::class, 'store']);

    Route::get('/trips/{trip}/journal', [JournalEntryController::class, 'index']);
    Route::post('/trips/{trip}/journal', [JournalEntryController::class, 'store']);

    Route::get('/stats/overview', StatsController::class);
});
