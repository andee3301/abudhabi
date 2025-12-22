<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        return view('trips.index');
    }

    public function show(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $trip->load([
            'itineraryItems' => fn ($query) => $query->with(['region', 'city'])->orderBy('start_datetime'),
            'journalEntries' => fn ($query) => $query->latest('entry_date'),
            'tripNotes' => fn ($query) => $query->latest('note_date')->latest('created_at'),
            'timelineEntries' => fn ($query) => $query->latest('occurred_at')->latest('created_at'),
            'events' => fn ($query) => $query->orderBy('position')->orderBy('start_time'),
            'countryVisits.region',
            'region',
            'city',
            'itineraries.items',
            'itineraries.city',
        ]);

        $cityStops = collect($trip->city_stops ?? []);
        $cityLookup = $cityStops->pluck('city_id')->filter()->isNotEmpty()
            ? \App\Models\City::whereIn('id', $cityStops->pluck('city_id')->filter())->get()->keyBy('id')
            : collect();

        $cityStops = $cityStops->map(function ($stop) use ($cityLookup) {
            $city = $stop['city_id'] ? $cityLookup->get($stop['city_id']) : null;

            return [
                'label' => $stop['label'] ?? $city?->display_name ?? $city?->name ?? 'Unknown city',
                'country_code' => $stop['country_code'] ?? $city?->country_code,
                'city' => $city,
            ];
        });

        return view('trips.show', [
            'trip' => $trip,
            'housing' => $trip->itineraryItems->where('type', 'housing'),
            'transport' => $trip->itineraryItems->where('type', 'transport'),
            'activities' => $trip->itineraryItems->where('type', 'activity'),
            'events' => $trip->events,
            'cityStops' => $cityStops,
            'wishlist' => $trip->wishlist_locations ?? [],
        ]);
    }
}
