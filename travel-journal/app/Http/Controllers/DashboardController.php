<?php

namespace App\Http\Controllers;

use App\Models\CountryVisit;
use App\Models\JournalEntry;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $upcomingTrips = Trip::whereBelongsTo($user)
            ->whereDate('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date')
            ->limit(4)
            ->get();

        $recentTrips = Trip::whereBelongsTo($user)
            ->orderByDesc('end_date')
            ->limit(3)
            ->get();

        $currentTrip = Trip::whereBelongsTo($user)
            ->where('status', 'ongoing')
            ->orderBy('start_date')
            ->first() ?? $upcomingTrips->first();

        $recentEntries = JournalEntry::with('trip')
            ->whereBelongsTo($user)
            ->latest('entry_date')
            ->limit(5)
            ->get();

        $stats = $this->buildStats($user);

        $housing = collect();
        $transport = collect();
        $activities = collect();
        $countryVisits = collect();

        if ($currentTrip) {
            $currentTrip->load(['itineraryItems' => fn ($q) => $q->orderBy('start_datetime'), 'countryVisits']);
            $housing = $currentTrip->itineraryItems->where('type', 'housing');
            $transport = $currentTrip->itineraryItems->where('type', 'transport');
            $activities = $currentTrip->itineraryItems->where('type', 'activity');
            $countryVisits = $currentTrip->countryVisits;
        }

        return view('dashboard', compact(
            'upcomingTrips',
            'recentTrips',
            'currentTrip',
            'recentEntries',
            'housing',
            'transport',
            'activities',
            'countryVisits',
            'stats'
        ));
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
}
