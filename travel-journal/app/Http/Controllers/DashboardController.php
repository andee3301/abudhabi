<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CountryVisit;
use App\Models\JournalEntry;
use App\Models\Trip;
use App\Services\CityIntelService;
use App\Support\DashboardCache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CityIntelService $intelService)
    {
        $user = $request->user()->load('homeSettings');

        $cards = DashboardCache::remember($user->id, 'cards', function () use ($user) {
            $upcomingTrips = Trip::with(['region', 'city'])
                ->whereBelongsTo($user)
                ->whereDate('start_date', '>=', now()->startOfDay())
                ->orderBy('start_date')
                ->limit(4)
                ->get();

            $recentTrips = Trip::with(['region', 'city'])
                ->whereBelongsTo($user)
                ->orderByDesc('end_date')
                ->orderByDesc('start_date')
                ->get();

            $currentTrip = Trip::with([
                'itineraryItems' => fn ($q) => $q->orderBy('start_datetime'),
                'countryVisits',
                'region',
                'city',
            ])
                ->whereBelongsTo($user)
                ->where('status', 'ongoing')
                ->orderBy('start_date')
                ->first() ?? $upcomingTrips->first();

            $recentEntries = JournalEntry::with('trip')
                ->whereBelongsTo($user)
                ->latest('entry_date')
                ->limit(5)
                ->get();

            $housing = collect();
            $transport = collect();
            $activities = collect();
            $countryVisits = collect();
            $timeline = collect();
            $mapTrips = collect();

            if ($currentTrip) {
                $housing = $currentTrip->itineraryItems->where('type', 'housing');
                $transport = $currentTrip->itineraryItems->where('type', 'transport');
                $activities = $currentTrip->itineraryItems->where('type', 'activity');
                $countryVisits = $currentTrip->countryVisits;
                $timeline = $this->buildTimeline($currentTrip);
            }

            $mapTrips = Trip::with('city')
                ->whereBelongsTo($user)
                ->orderByDesc('start_date')
                ->get();

            return compact(
                'upcomingTrips',
                'recentTrips',
                'currentTrip',
                'recentEntries',
                'housing',
                'transport',
                'activities',
                'countryVisits',
                'timeline',
                'mapTrips',
            );
        });

        $stats = DashboardCache::remember($user->id, 'stats', fn () => $this->buildStats($user));
        $regionChips = $cards['currentTrip'] ? $this->buildRegionChips($cards['currentTrip'], $cards['countryVisits']) : collect();
        $city = $this->resolveDashboardCity($request);
        $cityIntel = $city ? $intelService->dashboardPayload($city, $user) : null;
        $featuredCities = City::with('intel')->orderBy('name')->limit(20)->get();
        $homeCurrency = $user->homeSettings?->preferred_currency ?? 'USD';

        $normalize = function (Trip $trip) {
            $country = $trip->country_code ?? optional($trip->city)->country_code;
            $flag = $country ? sprintf('https://flagcdn.com/%s.svg', strtolower($country)) : asset('images/placeholder.svg');

            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'country_code' => $country,
                'city' => optional($trip->city)->name ?? $trip->primary_location_name,
                'lat' => optional($trip->city)->latitude,
                'lng' => optional($trip->city)->longitude,
                'image' => $trip->cover_url,
                'flag' => $flag,
                'progress' => $this->progressForTrip($trip),
                'mood' => $this->moodForTrip($trip),
                'url' => route('trips.show', $trip),
                'status' => $trip->status,
                'start' => $trip->start_date,
                'end' => $trip->end_date,
                'timezone' => $trip->timezone,
                'city_stops' => $trip->city_stops,
                'wishlist' => $trip->wishlist_locations,
                'location_overview' => $trip->location_overview,
            ];
        };

        $allTrips = $cards['mapTrips'] ?? collect();
        $journeys = collect($allTrips)->map($normalize)->values();

        $activeJourneys = collect([
            $cards['currentTrip'],
            ...$cards['upcomingTrips'],
        ])->filter()->unique('id')->map($normalize)->values();

        $pastJourneys = collect($cards['recentTrips'])->filter(function ($trip) {
            $ended = $trip->end_date && $trip->end_date->isPast();

            return $trip->status === 'completed' || $ended;
        })->unique('id')->map($normalize)->values();

        $mapPoints = $journeys->filter(fn ($trip) => ! is_null($trip['lat']) && ! is_null($trip['lng']))->values();

        return view('dashboard', [
            ...$cards,
            'regionChips' => $regionChips,
            'stats' => $stats,
            'cityIntel' => $cityIntel,
            'cityModel' => $city,
            'featuredCities' => $featuredCities,
            'homeSettings' => $user->homeSettings,
            'homeCurrency' => $homeCurrency,
            'journeys' => $journeys,
            'activeJourneys' => $activeJourneys,
            'pastJourneys' => $pastJourneys,
            'mapPoints' => $mapPoints,
        ]);
    }

    protected function progressForTrip(?Trip $trip): int
    {
        if (! $trip) {
            return 0;
        }

        if ($trip->status === 'completed') {
            return 100;
        }

        $start = $trip->start_date;
        $end = $trip->end_date;

        if ($start && $end) {
            $totalDays = max($start->diffInDays($end) + 1, 1);
            $now = now();
            $endpoint = $end->lt($now) ? $end : $now;
            $elapsed = $start->isFuture() ? 0 : min($totalDays, $start->diffInDays($endpoint) + 1);

            return min(100, (int) round(($elapsed / $totalDays) * 100));
        }

        return $trip->status === 'ongoing' ? 60 : 0;
    }

    protected function moodForTrip(?Trip $trip): ?string
    {
        if (! $trip) {
            return null;
        }

        $tagMood = collect($trip->tags ?? [])->first();

        if ($tagMood) {
            return $tagMood;
        }

        return match ($trip->status) {
            'ongoing' => '🧭 Adventurous',
            'planned' => '✨ Curious',
            'completed' => '🌧 Reflective',
            default => '🌱 Calm',
        };
    }

    protected function buildStats($user): array
    {
        $year = Carbon::now()->year;

        $tripsThisYear = Trip::whereBelongsTo($user)
            ->whereYear('start_date', $year)
            ->count();

        $totalTrips = Trip::whereBelongsTo($user)->count();

        $countriesVisited = CountryVisit::whereHas('trip', fn ($q) => $q->whereBelongsTo($user))
            ->selectRaw('COUNT(DISTINCT country_code) as count')
            ->value('count') ?? 0;

        $countryCounts = CountryVisit::whereHas('trip', fn ($q) => $q->whereBelongsTo($user))
            ->selectRaw('country_code, COUNT(*) as total')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'tripsThisYear' => $tripsThisYear,
            'totalTrips' => $totalTrips,
            'countriesVisited' => $countriesVisited,
            'countryCounts' => $countryCounts,
        ];
    }

    protected function buildTimeline(Trip $trip)
    {
        return $trip->itineraryItems
            ->sortBy('start_datetime')
            ->take(6)
            ->map(function ($item) use ($trip) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type,
                    'start' => optional($item->start_datetime)->timezone($trip->timezone ?? 'UTC'),
                    'end' => optional($item->end_datetime)->timezone($trip->timezone ?? 'UTC'),
                    'city' => $item->city ?? $item->location_name,
                    'timezone' => $item->timezone ?? $trip->timezone ?? 'UTC',
                    'status' => $item->status,
                ];
            });
    }

    protected function buildRegionChips(Trip $trip, $countryVisits)
    {
        $chips = collect();

        if ($trip->country_code) {
            $chips->push([
                'label' => strtoupper($trip->country_code),
                'tone' => 'indigo',
            ]);
        }

        if ($trip->state_region) {
            $chips->push([
                'label' => $trip->state_region,
                'tone' => 'emerald',
            ]);
        }

        if ($trip->timezone) {
            $chips->push([
                'label' => 'TZ '.$trip->timezone,
                'tone' => 'sky',
            ]);
        }

        $visitCountries = collect($countryVisits)->pluck('country_code')->filter()->unique();

        foreach ($visitCountries as $code) {
            $chips->push([
                'label' => 'Visit '.$code,
                'tone' => 'amber',
            ]);
        }

        return $chips;
    }

    protected function resolveDashboardCity(Request $request): ?City
    {
        $slug = $request->input('city');

        if ($slug) {
            return City::where('slug', $slug)->first();
        }

        $home = $request->user()?->homeSettings;

        if ($home?->home_city_id) {
            return City::find($home->home_city_id);
        }

        if ($home?->home_city_name) {
            return City::where('name', 'like', $home->home_city_name)->first();
        }

        return City::first();
    }
}
