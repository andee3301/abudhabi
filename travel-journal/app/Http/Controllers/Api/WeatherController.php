<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WeatherSnapshotResource;
use App\Jobs\FetchWeatherForTrip;
use App\Models\Trip;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function index(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        return WeatherSnapshotResource::collection(
            $trip->weatherSnapshots()->latest('recorded_at')->paginate(20)
        );
    }

    public function fetch(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        FetchWeatherForTrip::dispatch($trip);

        return response()->json(['queued' => true]);
    }
}
