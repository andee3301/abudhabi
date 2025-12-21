<?php

namespace App\Providers;

use App\Models\CountryVisit;
use App\Models\ItineraryItem;
use App\Models\JournalEntry;
use App\Models\Trip;
use App\Models\TripNote;
use App\Models\TripTimeline;
use App\Services\Analytics\Clients\GoogleAnalyticsClient;
use App\Services\Analytics\Contracts\AnalyticsClient;
use App\Services\Analytics\GoogleAnalyticsService;
use App\Support\DashboardCache;
use App\Support\FakeWeather;
use App\Support\TripCache;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BetaAnalyticsDataClient::class, function () {
            $credentials = config('services.google_analytics.credentials');

            $options = [];
            if ($credentials) {
                $options['credentials'] = $credentials;
            }

            return new BetaAnalyticsDataClient($options);
        });

        $this->app->singleton(AnalyticsClient::class, function ($app) {
            return new GoogleAnalyticsClient($app->make(BetaAnalyticsDataClient::class));
        });

        $this->app->singleton(GoogleAnalyticsService::class, function ($app) {
            return new GoogleAnalyticsService(
                $app->make(AnalyticsClient::class),
                config('services.google_analytics.property_id')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Trip::saving(function (Trip $trip): void {
            if ($trip->status === 'ongoing' && $trip->user_id) {
                Trip::where('user_id', $trip->user_id)
                    ->where('status', 'ongoing')
                    ->when($trip->exists, fn ($query) => $query->where('id', '!=', $trip->id))
                    ->update(['status' => 'planned']);
            }
        });

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

        TripNote::saved(function (TripNote $note): void {
            DashboardCache::flush($note->user_id);
            TripCache::flushTrip($note->trip_id, $note->user_id);
        });

        TripNote::deleted(function (TripNote $note): void {
            DashboardCache::flush($note->user_id);
            TripCache::flushTrip($note->trip_id, $note->user_id);
        });

        TripTimeline::saved(function (TripTimeline $timeline): void {
            DashboardCache::flush($timeline->user_id);
            TripCache::flushTrip($timeline->trip_id, $timeline->user_id);
        });

        TripTimeline::deleted(function (TripTimeline $timeline): void {
            DashboardCache::flush($timeline->user_id);
            TripCache::flushTrip($timeline->trip_id, $timeline->user_id);
        });

        $threshold = (int) env('SLOW_QUERY_THRESHOLD_MS', 300);
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

        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();
            $activeTrip = null;
            $weather = null;
            $timezone = config('app.timezone', 'UTC');

            if ($user) {
                $activeTrip = Trip::whereBelongsTo($user)
                    ->where('status', 'ongoing')
                    ->orderBy('start_date')
                    ->first();

                if (! $activeTrip) {
                    $activeTrip = Trip::whereBelongsTo($user)
                        ->orderBy('start_date')
                        ->first();
                }

                if ($activeTrip) {
                    $timezone = $activeTrip->timezone ?? $timezone;
                    $weather = FakeWeather::forTrip($activeTrip);
                }
            }

            $view->with('layoutActiveTrip', $activeTrip ? [
                'id' => $activeTrip->id,
                'title' => $activeTrip->title,
                'location' => $activeTrip->location_label,
                'timezone' => $timezone,
                'url' => route('trips.show', $activeTrip),
                'status' => $activeTrip->status,
            ] : null);

            $view->with('layoutWeather', $weather);
            $view->with('layoutTimezone', $timezone);
        });
    }
}
