<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\CityIntelService;
use App\Services\GeoLookupService;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function __invoke(Request $request, CityIntelService $intelService, GeoLookupService $geoLookup)
    {
        $destination = $request->input('q', 'Tokyo');

        $city = $geoLookup->findBySlugOrName($destination) ?? City::first();
        $intel = $city ? $intelService->dashboardPayload($city, $request->user()) : null;
        $suggestions = $geoLookup->search($destination, 6);
        $catalog = City::with('intel')->orderBy('name')->get();

        return view('explore.index', [
            'destination' => $destination,
            'city' => $city,
            'intel' => $intel,
            'suggestions' => $suggestions,
            'catalog' => $catalog,
        ]);
    }
}
