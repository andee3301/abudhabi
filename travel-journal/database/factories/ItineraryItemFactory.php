<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItineraryItem>
 */
class ItineraryItemFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['transport', 'housing', 'activity']);
        $start = $this->faker->dateTimeBetween('+1 days', '+2 months');
        $end = (clone $start)->modify('+'.random_int(1, 5).' hours');

        return [
            'trip_id' => Trip::factory(),
            'type' => $type,
            'title' => match ($type) {
                'transport' => 'Flight '.$this->faker->bothify('TK###'),
                'housing' => 'Stay at '.$this->faker->company().' Hotel',
                default => 'Explore '.$this->faker->city(),
            },
            'start_datetime' => $start,
            'end_datetime' => $end,
            'location_name' => $this->faker->city(),
            'address' => $this->faker->address(),
            'price' => $this->faker->randomFloat(2, 50, 1200),
            'currency' => 'USD',
            'status' => $this->faker->randomElement(['booked', 'tentative', 'completed']),
            'metadata' => [],
        ];
    }
}
