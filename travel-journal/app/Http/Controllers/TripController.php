<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('trips.index', [
            'trips' => Trip::with('latestWeather')
                ->whereBelongsTo($user)
                ->latest('start_date')
                ->paginate(10),
        ]);
    }

    public function show(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $trip->load([
            'journalEntries.media',
            'weatherSnapshots' => fn ($query) => $query->latest('recorded_at'),
            'latestWeather',
        ]);

        return view('trips.show', [
            'trip' => $trip,
        ]);
    }
}
