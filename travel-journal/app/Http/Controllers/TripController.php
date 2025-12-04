<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::withCount(['journalEntries', 'itineraryItems'])
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

        return view('trips.index', [
            'trips' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'q' => $request->q,
                'status' => $request->status,
            ],
        ]);
    }

    public function show(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $trip->load([
            'itineraryItems' => fn ($query) => $query->orderBy('start_datetime'),
            'journalEntries' => fn ($query) => $query->latest('entry_date'),
            'countryVisits',
        ]);

        return view('trips.show', [
            'trip' => $trip,
            'housing' => $trip->itineraryItems->where('type', 'housing'),
            'transport' => $trip->itineraryItems->where('type', 'transport'),
            'activities' => $trip->itineraryItems->where('type', 'activity'),
        ]);
    }
}
