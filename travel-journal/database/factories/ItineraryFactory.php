<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Itinerary;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Itinerary>
 */
class ItineraryFactory extends Factory
{
    protected $model = Itinerary::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-30 days', '+30 days');
        $end = (clone $start);
        $end->modify('+'.random_int(0, 6).' days');

        return [
            'trip_id' => Trip::factory(),
            'city_id' => City::factory(),
            'title' => $this->faker->sentence(3),
            'day_count' => 1,
            'start_date' => $start,
            'end_date' => $end,
            'is_primary' => false,
            'theme' => null,
            'metadata' => null,
        ];
    }

    public function primary(): self
    {
        return $this->state(fn () => ['is_primary' => true]);
    }
}
