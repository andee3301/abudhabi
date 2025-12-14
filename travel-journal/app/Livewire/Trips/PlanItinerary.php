<?php

namespace App\Livewire\Trips;

use App\Models\Itinerary;
use App\Models\Trip;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PlanItinerary extends Component
{
    public Trip $trip;

    public string $type = 'activity';
    public string $title = '';
    public ?string $start_datetime = '';
    public ?string $end_datetime = '';
    public ?int $day_number = null;
    public ?string $location_name = '';
    public ?string $city = '';
    public ?string $state_region = '';
    public ?string $country_code = '';
    public ?string $timezone = '';
    public ?string $status = 'tentative';
    public ?int $city_id = null;
    public ?int $itinerary_id = null;

    public array $timezones = [];

    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['transport', 'housing', 'activity', 'note'])],
            'title' => ['required', 'string', 'max:255'],
            'start_datetime' => ['nullable', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'day_number' => ['nullable', 'integer', 'min:0'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_region' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'timezone' => ['required', 'string', 'max:64'],
            'status' => ['nullable', 'string', 'max:50'],
            'city_id' => ['nullable', 'integer'],
            'itinerary_id' => ['nullable', 'integer'],
        ];
    }

    public function mount(Trip $trip): void
    {
        $this->trip = $trip;
        $this->timezone = $trip->timezone ?? 'UTC';
        $this->country_code = $trip->country_code;
        $this->city = $trip->city;
        $this->state_region = $trip->state_region;
        $this->city_id = $trip->city_id;
        $this->itinerary_id = $trip->itineraries()->orderByDesc('is_primary')->value('id');

        $this->timezones = collect(['UTC'])
            ->concat(collect(\DateTimeZone::listIdentifiers())->take(60))
            ->unique()
            ->values()
            ->all();
    }

    public function prefillFromTrip(): void
    {
        $this->timezone = $this->trip->timezone ?? 'UTC';
        $this->country_code = $this->trip->country_code;
        $this->city = $this->trip->city;
        $this->state_region = $this->trip->state_region;
        $this->city_id = $this->trip->city_id;
    }

    protected function resolveItineraryId(): ?int
    {
        if ($this->itinerary_id && $this->trip->itineraries()->whereKey($this->itinerary_id)->exists()) {
            return $this->itinerary_id;
        }

        $itinerary = $this->trip->itineraries()->orderByDesc('is_primary')->first();

        if (! $itinerary) {
            $itinerary = $this->trip->itineraries()->create([
                'title' => $this->trip->title.' Itinerary',
                'city_id' => $this->trip->city_id,
                'start_date' => $this->trip->start_date,
                'end_date' => $this->trip->end_date,
                'day_count' => ($this->trip->start_date && $this->trip->end_date) ? $this->trip->start_date->diffInDays($this->trip->end_date) + 1 : 0,
                'is_primary' => true,
            ]);
        }

        $this->itinerary_id = $itinerary->id;

        return $this->itinerary_id;
    }

    public function addItem(): void
    {
        $validated = $this->validate();
        $validated['country_code'] = $validated['country_code'] ?: $this->trip->country_code;
        $validated['state_region'] = $validated['state_region'] ?: $this->trip->state_region;
        $validated['city'] = $validated['city'] ?: $this->trip->city;
        $validated['timezone'] = $validated['timezone'] ?: ($this->trip->timezone ?? 'UTC');
        $validated['region_id'] = $this->trip->region_id;
        $validated['location_name'] = $validated['location_name'] ?: $validated['city'];
        $validated['city_id'] = $validated['city_id'] ?: $this->trip->city_id;
        $validated['itinerary_id'] = $this->resolveItineraryId();

        if (! $validated['day_number'] && $validated['start_datetime'] && $this->trip->start_date) {
            $validated['day_number'] = Carbon::parse($this->trip->start_date)->diffInDays(Carbon::parse($validated['start_datetime'])) + 1;
        }

        $this->trip->itineraryItems()->create($validated);
        $this->trip->refresh();

        $this->reset(['title', 'start_datetime', 'end_datetime', 'location_name', 'city', 'state_region', 'status', 'day_number']);
        $this->timezone = $this->trip->timezone ?? 'UTC';
        $this->country_code = $this->trip->country_code;

        $this->dispatch('itineraryUpdated');
    }

    public function render()
    {
        $upcoming = $this->trip->itineraryItems()
            ->orderByRaw('COALESCE(day_number, 0), COALESCE(start_datetime, created_at)')
            ->limit(5)
            ->get();

        return view('livewire.trips.plan-itinerary', [
            'upcoming' => $upcoming,
        ]);
    }
}
