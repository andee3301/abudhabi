<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\ItineraryItemController;
use App\Http\Controllers\Api\JournalEntryController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TripController as ApiTripController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthTokenController::class, 'store']);

Route::middleware(['auth:sanctum', 'ability.token'])->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());

    Route::get('/trips', [ApiTripController::class, 'index'])->middleware('abilities:trips:read');
    Route::post('/trips', [ApiTripController::class, 'store'])->middleware('abilities:trips:write');
    Route::get('/trips/{trip}', [ApiTripController::class, 'show'])->middleware('abilities:trips:read');
    Route::put('/trips/{trip}', [ApiTripController::class, 'update'])->middleware('abilities:trips:write');
    Route::delete('/trips/{trip}', [ApiTripController::class, 'destroy'])->middleware('abilities:trips:write');

    Route::get('/cities', [CityController::class, 'index'])->middleware('abilities:cities:read');
    Route::get('/cities/{city}', [CityController::class, 'show'])->middleware('abilities:cities:read');
    Route::get('/cities/{city}/intel', [CityController::class, 'intel'])->middleware('abilities:cities:read');

    Route::get('/trips/{trip}/itineraries', [ItineraryController::class, 'index'])->middleware('abilities:itinerary:read');
    Route::post('/trips/{trip}/itineraries', [ItineraryController::class, 'store'])->middleware('abilities:itinerary:write');
    Route::put('/trips/{trip}/itineraries/{itinerary}', [ItineraryController::class, 'update'])->middleware('abilities:itinerary:write');

    Route::get('/trips/{trip}/itinerary', [ItineraryItemController::class, 'index'])->middleware('abilities:itinerary:read');
    Route::post('/trips/{trip}/itinerary', [ItineraryItemController::class, 'store'])->middleware('abilities:itinerary:write');

    Route::get('/trips/{trip}/journal', [JournalEntryController::class, 'index'])->middleware('abilities:journal:read');
    Route::post('/trips/{trip}/journal', [JournalEntryController::class, 'store'])->middleware('abilities:journal:write');

    Route::get('/stats/overview', StatsController::class)->middleware('abilities:stats:read');
});
