<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\WeatherSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\SerializesModels;

class FetchWeatherForTrip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 15;

    public function __construct(public Trip $trip) {}

    public function handle(): void
    {
        $recordedAt = now($this->trip->timezone ?? config('app.timezone'));

        WeatherSnapshot::updateOrCreate(
            [
                'trip_id' => $this->trip->id,
                'provider' => 'demo',
                'recorded_at' => $recordedAt,
            ],
            [
                // Static but believable snapshot to keep offline demo intact.
                'temperature' => 18.5,
                'humidity' => 62,
                'wind_speed' => 4.2,
                'conditions' => 'Clear',
                'icon' => 'demo-clear',
                'payload' => [
                    'note' => 'Static demo weather; no external API used.',
                ],
            ]
        );
    }
}
