<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Trip::with('latestWeather')
            ->whereBelongsTo(request()->user())
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('destination', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('start_date');

        return TripResource::collection($query->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['planned', 'in_progress', 'completed'])],
            'notes' => ['nullable', 'string'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['user_id'] = $request->user()->id;

        $trip = Trip::create($validated);

        return (new TripResource($trip->fresh('latestWeather')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        abort_unless($trip->user_id === request()->user()->id, 403);

        return new TripResource(
            $trip->load(['journalEntries.media', 'weatherSnapshots', 'latestWeather'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trip $trip)
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'destination' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', Rule::in(['planned', 'in_progress', 'completed'])],
            'notes' => ['nullable', 'string'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $trip->update($validated);

        return new TripResource($trip->fresh('latestWeather'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        abort_unless($trip->user_id === request()->user()->id, 403);

        $trip->delete();

        return response()->noContent();
    }
}
