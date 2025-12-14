<?php

namespace App\Providers;

use App\Models\CountryVisit;
use App\Models\ItineraryItem;
use App\Models\JournalEntry;
use App\Models\Trip;
use App\Support\DashboardCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Trip::saved(function (Trip $trip): void {
            DashboardCache::flush($trip->user_id);
        });

        Trip::deleted(function (Trip $trip): void {
            DashboardCache::flush($trip->user_id);
        });

        ItineraryItem::saved(function (ItineraryItem $item): void {
            $userId = $item->trip->user_id ?? $item->trip()->value('user_id');
            if ($userId) {
                DashboardCache::flush($userId);
            }
        });

        ItineraryItem::deleted(function (ItineraryItem $item): void {
            $userId = $item->trip->user_id ?? $item->trip()->value('user_id');
            if ($userId) {
                DashboardCache::flush($userId);
            }
        });

        JournalEntry::saved(function (JournalEntry $entry): void {
            $userId = $entry->trip->user_id ?? $entry->trip()->value('user_id');
            if ($userId) {
                DashboardCache::flush($userId);
            }
        });

        JournalEntry::deleted(function (JournalEntry $entry): void {
            $userId = $entry->trip->user_id ?? $entry->trip()->value('user_id');
            if ($userId) {
                DashboardCache::flush($userId);
            }
        });

        CountryVisit::saved(function (CountryVisit $visit): void {
            $userId = $visit->trip->user_id ?? $visit->trip()->value('user_id');
            if ($userId) {
                DashboardCache::flush($userId);
            }
        });

        CountryVisit::deleted(function (CountryVisit $visit): void {
            $userId = $visit->trip->user_id ?? $visit->trip()->value('user_id');
            if ($userId) {
                DashboardCache::flush($userId);
            }
        });

        $threshold = (int) env('SLOW_QUERY_THRESHOLD_MS', 0);
        if ($threshold > 0) {
            DB::listen(function ($query) use ($threshold): void {
                if ($query->time >= $threshold) {
                    Log::warning('slow_query', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time_ms' => $query->time,
                    ]);
                }
            });
        }
    }
}
