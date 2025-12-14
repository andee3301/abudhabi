<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeatherSnapshot>
 */
class WeatherSnapshotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'recorded_at' => $this->faker->dateTimeBetween('-1 day', '+1 day'),
            'provider' => 'demo',
            'temperature' => $this->faker->randomFloat(1, -5, 35),
            'humidity' => $this->faker->numberBetween(20, 95),
            'wind_speed' => $this->faker->randomFloat(1, 0, 15),
            'conditions' => $this->faker->randomElement(['Clear', 'Clouds', 'Rain', 'Snow']),
            'icon' => '01d',
            'payload' => null,
        ];
    }
}
