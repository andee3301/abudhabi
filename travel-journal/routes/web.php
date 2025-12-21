<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\CityLookupController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\CityGuideController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->middleware('cache.headers:public;max_age=600;etag');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('trips/{trip}', [TripController::class, 'show'])->name('trips.show');
    Route::get('explore', ExploreController::class)->name('explore.index');
    Route::get('cities/search', [CityLookupController::class, 'search'])->name('cities.search');
    Route::get('cities/{city}', [CityLookupController::class, 'show'])->name('cities.show');
    Route::get('city-guides/{city}', [CityGuideController::class, 'show'])->name('cities.guide');

    Route::get('journal/create', [JournalController::class, 'create'])->name('journal.create');
    Route::post('journal', [JournalController::class, 'store'])->name('journal.store');

    Route::view('profile', 'profile')->name('profile');
    Route::post('profile/api-token', [ApiTokenController::class, 'store'])->name('profile.api-token.store');
    Route::view('docs/api', 'scramble::docs')->name('docs.api');
});

require __DIR__.'/auth.php';
