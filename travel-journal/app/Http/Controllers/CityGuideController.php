<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\CityIntelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityGuideController extends Controller
{
    public function show(Request $request, City $city, CityIntelService $intelService)
    {
        $intel = $intelService->dashboardPayload($city, $request->user());

        $relatedTrips = \App\Models\Trip::whereBelongsTo($request->user())
            ->where(function ($query) use ($city) {
                $query->where('city_id', $city->id)
                    ->orWhereJsonContains('city_stops', ['city_id' => $city->id])
                    ->orWhereJsonContains('city_stops', $city->id, '$[*].city_id');

                if (DB::getDriverName() === 'sqlite') {
                    $query->orWhere('city_stops', 'like', '%"city_id":'.$city->id.'%');
                }
            })
            ->latest('start_date')
            ->get();

        return view('cities.show', [
            'city' => $city,
            'intel' => $intel,
            'relatedTrips' => $relatedTrips,
        ]);
    }
}
