<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::with(['region', 'city'])
            ->withCount(['journalEntries', 'itineraryItems', 'tripNotes', 'timelineEntries'])
            ->whereBelongsTo($request->user())
            ->when($request->filled('q'), function ($builder) use ($request) {
                $builder->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%'.$request->q.'%')
                        ->orWhere('primary_location_name', 'like', '%'.$request->q.'%')
                        ->orWhere('notes', 'like', '%'.$request->q.'%');
                });
            })
            ->when($request->filled('status'), function ($builder) use ($request) {
                $builder->where('status', $request->status);
            })
            ->orderByDesc('start_date');

        $trips = $query->paginate(12)->withQueryString();

        return view('trips.index', [
            'trips' => $trips,
            'filters' => [
                'q' => $request->q,
                'status' => $request->status,
            ],
            'journeys' => $trips->items(),
        ]);
    }

    public function show(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $trip->load([
            'itineraryItems' => fn ($query) => $query->with(['region', 'city'])->orderBy('start_datetime'),
            'journalEntries' => fn ($query) => $query->latest('entry_date'),
            'tripNotes' => fn ($query) => $query->latest('note_date')->latest('created_at'),
            'timelineEntries' => fn ($query) => $query->latest('occurred_at')->latest('created_at'),
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
            'cityStops' => $cityStops,
            'wishlist' => $trip->wishlist_locations ?? [],
        ]);
    }
}
