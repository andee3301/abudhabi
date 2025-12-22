<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\TripEvent;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TripTimelineBoard extends Component
{
    public Trip $trip;

    public array $events = [];

    public ?int $editingEventId = null;

    #[Validate('required|string|in:location,hotel,travel,note')]
    public string $type = 'location';

    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $description = null;

    public ?string $start_time = null;

    public ?string $end_time = null;

    public ?string $travel_method = null;

    public array $location_data = [];

    public function mount(Trip $trip): void
    {
        $this->trip = $trip;
        $this->backfillFromItinerary();
        $this->refreshEvents();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'type' => ['required', Rule::in(['location', 'hotel', 'travel', 'note'])],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'travel_method' => ['nullable', 'string', 'max:100'],
            'location_data' => ['array'],
        ]);

        $payload = [
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_time' => $validated['start_time'] ?: null,
            'end_time' => $validated['end_time'] ?: null,
            'location_data' => ! empty($validated['location_data']) ? $validated['location_data'] : null,
            'travel_method' => $validated['travel_method'],
        ];

        if ($this->editingEventId) {
            $event = $this->findEvent($this->editingEventId);
            $event->update($payload);
            session()->flash('status', 'Event updated.');
        } else {
            $position = ($this->trip->events()->max('position') ?? 0) + 1;
            $payload['position'] = $position;
            $this->trip->events()->create($payload);
            session()->flash('status', 'Event added.');
        }

        $this->resetEventForm();
        $this->dispatch('timeline-saved');
        $this->refreshEvents();
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $eventId) {
            TripEvent::where('trip_id', $this->trip->id)
                ->where('id', $eventId)
                ->update(['position' => $index + 1]);
        }

        $this->refreshEvents();
    }

    public function deleteEvent(int $eventId): void
    {
        TripEvent::where('trip_id', $this->trip->id)->where('id', $eventId)->delete();
        $this->refreshEvents();
    }

    public function startEditing(int $eventId): void
    {
        $event = $this->findEvent($eventId);
        $this->editingEventId = $event->id;
        $this->type = $event->type;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->start_time = $this->formatForInput($event->start_time);
        $this->end_time = $this->formatForInput($event->end_time);
        $this->travel_method = $event->travel_method;
        $this->location_data = $event->location_data ?? [];
        $this->resetErrorBag();
        $this->dispatch('open-event-modal');
    }

    public function cancelEditing(): void
    {
        $this->resetEventForm();
    }

    public function render()
    {
        return view('livewire.trips.trip-timeline-board');
    }

    protected function refreshEvents(): void
    {
        $this->events = $this->trip->events()
            ->orderBy('position')
            ->orderBy('start_time')
            ->get()
            ->map(fn (TripEvent $event) => [
                'id' => $event->id,
                'type' => $event->type,
                'title' => $event->title,
                'description' => $event->description,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'travel_method' => $event->travel_method,
                'location_data' => $event->location_data,
            ])->toArray();
    }

    protected function resetEventForm(): void
    {
        $this->reset(['editingEventId', 'title', 'description', 'start_time', 'end_time', 'travel_method', 'location_data']);
        $this->type = 'location';
        $this->resetErrorBag();
    }

    protected function findEvent(int $eventId): TripEvent
    {
        return TripEvent::where('trip_id', $this->trip->id)->findOrFail($eventId);
    }

    protected function formatForInput($value): ?string
    {
        return $value ? $value->format('Y-m-d\TH:i') : null;
    }

    protected function backfillFromItinerary(): void
    {
        if ($this->trip->events()->exists()) {
            return;
        }

        $items = $this->trip->itineraryItems()
            ->orderBy('start_datetime')
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        $position = 1;

        foreach ($items as $item) {
            $type = match ($item->type) {
                'housing' => 'hotel',
                'transport' => 'travel',
                default => 'location',
            };

            $title = $item->title ?: ($item->location_name ?? $item->city ?? 'Itinerary item');

            $this->trip->events()->create([
                'type' => $type,
                'title' => $title,
                'description' => $item->address,
                'start_time' => $item->start_datetime,
                'end_time' => $item->end_datetime,
                'location_data' => $item->address ? ['address' => $item->address] : null,
                'travel_method' => $type === 'travel' ? ($item->metadata['method'] ?? null) : null,
                'position' => $position++,
            ]);
        }
    }
}
