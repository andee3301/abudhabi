<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Services\DestinationMediaService;
use Livewire\Component;

class TripHero extends Component
{
    public Trip $trip;

    public array $media = [];

    public function mount(Trip $trip, DestinationMediaService $mediaService): void
    {
        $city = $trip->city?->name ?? $trip->primary_location_name ?? $trip->title;
        $this->media = $mediaService->for($city, $trip->country_code);
    }

    public function render()
    {
        return view('livewire.trips.trip-hero');
    }
}
