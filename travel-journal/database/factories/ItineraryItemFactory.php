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
        $stateRegion = $this->faker->state();
        $country = $this->faker->countryCode();
        $timezone = $this->faker->timezone();

        return [
            'trip_id' => Trip::factory(),
            'itinerary_id' => null,
            'city_id' => \App\Models\City::factory()->state([
                'country_code' => $country,
                'state_region' => $stateRegion,
                'timezone' => $timezone,
            ]),
            'type' => $type,
            'title' => match ($type) {
                'transport' => 'Flight '.$this->faker->bothify('TK###'),
                'housing' => 'Stay at '.$this->faker->company().' Hotel',
                default => 'Explore '.$this->faker->city(),
            },
            'start_datetime' => $start,
            'end_datetime' => $end,
            'day_number' => 1,
            'sort_order' => 10,
            'location_name' => $this->faker->city(),
            'city' => $this->faker->city(),
            'state_region' => $stateRegion,
            'country_code' => $country,
            'timezone' => $timezone,
            'region_id' => \App\Models\Region::factory()->state([
                'country_code' => $country,
                'name' => $stateRegion,
                'default_timezone' => $timezone,
            ]),
            'address' => $this->faker->address(),
            'price' => $this->faker->randomFloat(2, 50, 1200),
            'currency' => 'USD',
            'status' => $this->faker->randomElement(['booked', 'tentative', 'completed']),
            'metadata' => [],
            'links' => ['map' => $this->faker->url()],
            'tags' => ['seed'],
        ];
    }
}
