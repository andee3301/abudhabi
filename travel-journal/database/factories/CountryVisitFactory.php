<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CountryVisit>
 */
class CountryVisitFactory extends Factory
{
    public function definition(): array
    {
        $stateRegion = $this->faker->state();
        $country = $this->faker->countryCode();
        $timezone = $this->faker->timezone();

        return [
            'trip_id' => Trip::factory(),
            'country_code' => $country,
            'city_name' => $this->faker->city(),
            'state_region' => $stateRegion,
            'timezone' => $timezone,
            'region_id' => \App\Models\Region::factory()->state([
                'country_code' => $country,
                'name' => $stateRegion,
                'default_timezone' => $timezone,
            ]),
            'visited_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
