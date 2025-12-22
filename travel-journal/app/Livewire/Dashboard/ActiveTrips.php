<?php

namespace App\Livewire\Dashboard;

use App\Models\Trip;
use App\Support\MarketingAssetRepository;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ActiveTrips extends Component
{
    public array $trips = [];

    public function mount(): void
    {
        $this->trips = $this->queryActiveTrips();
    }

    public function render()
    {
        return view('livewire.dashboard.active-trips');
    }

    protected function queryActiveTrips(): array
    {
        $user = auth()->user();

        $trips = Trip::with(['city'])
            ->whereBelongsTo($user)
            ->where('status', 'ongoing')
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        $assets = app(MarketingAssetRepository::class);

        return $trips->map(function (Trip $trip) use ($assets) {
            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'city' => $trip->city?->name ?? $trip->primary_location_name,
                'country_code' => $trip->country_code ?? $trip->city?->country_code,
                'flag' => $trip->country_code ? sprintf('https://flagcdn.com/%s.svg', strtolower($trip->country_code)) : null,
                'start' => $trip->start_date,
                'end' => $trip->end_date,
                'status' => $trip->status,
                'progress' => $this->itineraryCompletion($trip),
                'image' => $trip->cover_image_url ? $trip->cover_url : $assets->url('trip_cover_default'),
                'url' => route('trips.show', $trip),
            ];
        })->all();
    }

    protected function itineraryCompletion(Trip $trip): int
    {
        $total = $trip->itineraryItems()->count();
        $done = $trip->itineraryItems()->whereIn('status', ['done', 'completed'])->count();

        if ($total > 0) {
            return min(100, (int) round(($done / $total) * 100));
        }

        $start = $trip->start_date;
        $end = $trip->end_date;
        if ($start && $end) {
            $totalDays = max($start->diffInDays($end) + 1, 1);
            $now = Carbon::now();
            $endpoint = $end->lt($now) ? $end : $now;
            $elapsed = $start->isFuture() ? 0 : min($totalDays, $start->diffInDays($endpoint) + 1);

            return min(100, (int) round(($elapsed / $totalDays) * 100));
        }

        return $trip->status === 'ongoing' ? 60 : 0;
    }
}
