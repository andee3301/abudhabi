<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TripEventController extends Controller
{
    public function index(Request $request, Trip $trip)
    {
        $this->authorizeTrip($request, $trip);

        return $trip->events()->orderBy('position')->orderBy('start_time')->get();
    }

    public function store(Request $request, Trip $trip)
    {
        $this->authorizeTrip($request, $trip);

        $data = $this->validatePayload($request);
        $data['position'] = ($trip->events()->max('position') ?? 0) + 1;

        $event = $trip->events()->create($data);

        return response()->json($event, 201);
    }

    public function update(Request $request, Trip $trip, TripEvent $event)
    {
        $this->authorizeTrip($request, $trip);
        abort_unless($event->trip_id === $trip->id, 404);

        $data = $this->validatePayload($request, partial: true);
        $event->update($data);

        return $event->fresh();
    }

    public function destroy(Request $request, Trip $trip, TripEvent $event)
    {
        $this->authorizeTrip($request, $trip);
        abort_unless($event->trip_id === $trip->id, 404);

        $event->delete();

        return response()->noContent();
    }

    protected function authorizeTrip(Request $request, Trip $trip): void
    {
        abort_unless($trip->user_id === $request->user()->id, 403);
    }

    protected function validatePayload(Request $request, bool $partial = false): array
    {
        $rules = [
            'type' => ['required', Rule::in(['location', 'hotel', 'travel', 'note'])],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'location_data' => ['nullable', 'array'],
            'travel_method' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];

        if ($partial) {
            foreach ($rules as &$rule) {
                array_unshift($rule, 'sometimes');
            }
        }

        return $request->validate($rules);
    }
}
