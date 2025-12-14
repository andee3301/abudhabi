<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use App\Services\CityIntelService;
use App\Services\GeoLookupService;
use Illuminate\Http\Request;

class CityLookupController extends Controller
{
    public function search(Request $request, GeoLookupService $geoLookup)
    {
        $cities = $geoLookup->search($request->get('q', ''), 10);

        return CityResource::collection($cities);
    }

    public function show(Request $request, City $city, CityIntelService $intelService)
    {
        $payload = $intelService->dashboardPayload($city, $request->user());

        return response()->json($payload);
    }
}
