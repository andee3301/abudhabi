<?php

namespace App\Support;

use App\Models\Trip;

class FakeWeather
{
    /**
     * Generate a stable, fake weather snapshot for a trip.
     */
    public static function forTrip(Trip $trip): array
    {
        $label = $trip->location_label ?: ($trip->primary_location_name ?: $trip->title);
        $seed = crc32(strtolower($label ?? 'anywhere')) ?: 1;
        $conditions = [
            'Sunny',
            'Partly cloudy',
            'Scattered clouds',
            'Light rain',
            'Breezy',
            'Clear',
        ];

        $condition = $conditions[$seed % count($conditions)];
        $tempC = 12 + ($seed % 18); // 12°C–29°C
        $tempF = (int) round(($tempC * 9 / 5) + 32);

        return [
            'location' => $label ?: 'On the move',
            'condition' => $condition,
            'temperature_c' => $tempC,
            'temperature_f' => $tempF,
            'icon' => match ($condition) {
                'Light rain' => '🌦',
                'Breezy' => '🌬',
                'Scattered clouds', 'Partly cloudy' => '⛅️',
                'Sunny' => '☀️',
                default => '🌤',
            },
            'timezone' => $trip->timezone ?? config('app.timezone', 'UTC'),
        ];
    }
}
