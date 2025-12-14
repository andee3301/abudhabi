<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityIntelResource;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Services\CityIntelService;
use App\Services\GeoLookupService;
use App\Support\ChecksAbilities;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use ChecksAbilities;

    public function index(Request $request, GeoLookupService $geoLookup)
    {
        $this->ensureAbility($request, 'cities:read');

        $cities = $geoLookup->search($request->get('q', ''), min(15, $request->integer('limit', 8)));

        return CityResource::collection($cities);
    }

    public function show(Request $request, City $city, CityIntelService $intelService)
    {
        $this->ensureAbility($request, 'cities:read');

        $payload = $intelService->dashboardPayload($city, $request->user());

        return response()->json($payload);
    }

    public function intel(Request $request, City $city, CityIntelService $intelService)
    {
        $this->ensureAbility($request, 'cities:read');

        $intel = $intelService->intel($city);

        return (new CityIntelResource($intel))->additional([
            'city' => new CityResource($city),
        ]);
    }
}
