<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\WeatherSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchWeatherForTrip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Trip $trip)
    {
    }

    public function handle(): void
    {
        $apiKey = config('services.openweather.key');

        if (! $apiKey) {
            Log::warning('OpenWeather API key missing; skipping weather fetch.', ['trip_id' => $this->trip->id]);

            return;
        }

        $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
            'q' => $this->trip->destination,
            'appid' => $apiKey,
            'units' => 'metric',
        ]);

        if (! $response->successful()) {
            Log::warning('OpenWeather request failed', [
                'trip_id' => $this->trip->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return;
        }

        $payload = $response->json();
        $recordedAt = now($this->trip->timezone ?? config('app.timezone'));

        WeatherSnapshot::updateOrCreate(
            [
                'trip_id' => $this->trip->id,
                'provider' => 'openweathermap',
                'recorded_at' => $recordedAt,
            ],
            [
                'temperature' => data_get($payload, 'main.temp'),
                'humidity' => data_get($payload, 'main.humidity'),
                'wind_speed' => data_get($payload, 'wind.speed'),
                'conditions' => data_get($payload, 'weather.0.main'),
                'icon' => data_get($payload, 'weather.0.icon'),
                'payload' => $payload,
            ]
        );
    }
}
