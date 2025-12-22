<?php

namespace App\Livewire\Dashboard;

use App\Models\Trip;
use Livewire\Component;

class PastTrips extends Component
{
    public array $trips = [];

    public bool $hasMore = false;

    public int $perPage = 3;

    protected $listeners = [
        'pastTrips:load-more' => 'loadMore',
    ];

    public function mount(): void
    {
        $this->hydrateTrips();
    }

    public function loadMore(): void
    {
        $this->perPage += 3;
        $this->hydrateTrips();
    }

    public function render()
    {
        return view('livewire.dashboard.past-trips');
    }

    protected function hydrateTrips(): void
    {
        $user = auth()->user();

        $query = Trip::with(['city'])
            ->whereBelongsTo($user)
            ->where(function ($q) {
                $q->where('status', 'completed')->orWhereNotNull('end_date');
            })
            ->orderByDesc('end_date')
            ->orderByDesc('start_date');

        $page = $query->paginate($this->perPage, ['*'], 'pastTripsPage', 1);

        $this->hasMore = $page->hasMorePages();

        $this->trips = $page->getCollection()->map(function (Trip $trip) {
            $completed = 100;
            if ($trip->status !== 'completed' && $trip->end_date && $trip->end_date->isFuture()) {
                $completed = 80;
            }

            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'city' => $trip->city?->name ?? $trip->primary_location_name,
                'country_code' => $trip->country_code ?? $trip->city?->country_code,
                'flag' => $trip->country_code ? sprintf('https://flagcdn.com/%s.svg', strtolower($trip->country_code)) : null,
                'start' => $trip->start_date,
                'end' => $trip->end_date,
                'status' => $trip->status,
                'progress' => $completed,
                'image' => $trip->cover_url,
                'url' => route('trips.show', $trip),
            ];
        })->all();
    }
}
