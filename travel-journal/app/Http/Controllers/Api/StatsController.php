<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CountryVisit;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $year = Carbon::now()->year;

        $tripsThisYear = Trip::whereBelongsTo($user)
            ->whereYear('start_date', $year)
            ->count();

        $totalTrips = Trip::whereBelongsTo($user)->count();

        $countriesVisited = CountryVisit::whereHas('trip', fn ($q) => $q->whereBelongsTo($user))
            ->distinct('country_code')
            ->count('country_code');

        $countryCounts = CountryVisit::whereHas('trip', fn ($q) => $q->whereBelongsTo($user))
            ->selectRaw('country_code, COUNT(*) as total')
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json([
            'trips_this_year' => $tripsThisYear,
            'total_trips' => $totalTrips,
            'countries_visited' => $countriesVisited,
            'countries' => $countryCounts,
        ]);
    }
}
