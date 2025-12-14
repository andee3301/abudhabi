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
            $upcomingTrips = Trip::with('region')
                ->whereBelongsTo($user)
                ->whereDate('start_date', '>=', now()->startOfDay())
                ->orderBy('start_date')
                ->limit(4)
                ->get();

            $recentTrips = Trip::with('region')
                ->whereBelongsTo($user)
                ->orderByDesc('end_date')
                ->limit(3)
                ->get();

            $currentTrip = Trip::with([
                'itineraryItems' => fn ($q) => $q->orderBy('start_datetime'),
                'countryVisits',
                'region',
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

            if ($currentTrip) {
                $housing = $currentTrip->itineraryItems->where('type', 'housing');
                $transport = $currentTrip->itineraryItems->where('type', 'transport');
                $activities = $currentTrip->itineraryItems->where('type', 'activity');
                $countryVisits = $currentTrip->countryVisits;
                $timeline = $this->buildTimeline($currentTrip);
            }

            return compact(
                'upcomingTrips',
                'recentTrips',
                'currentTrip',
                'recentEntries',
                'housing',
                'transport',
                'activities',
                'countryVisits',
                'timeline'
            );
        });

        $stats = DashboardCache::remember($user->id, 'stats', fn () => $this->buildStats($user));
        $regionChips = $cards['currentTrip'] ? $this->buildRegionChips($cards['currentTrip'], $cards['countryVisits']) : collect();
        $city = $this->resolveDashboardCity($request);
        $cityIntel = $city ? $intelService->dashboardPayload($city, $user) : null;
        $featuredCities = City::with('intel')->orderBy('name')->limit(20)->get();
        $homeCurrency = $user->homeSettings?->preferred_currency ?? 'USD';

        return view('dashboard', [
            ...$cards,
            'regionChips' => $regionChips,
            'stats' => $stats,
            'cityIntel' => $cityIntel,
            'cityModel' => $city,
            'featuredCities' => $featuredCities,
            'homeSettings' => $user->homeSettings,
            'homeCurrency' => $homeCurrency,
        ]);
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
