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
        return [
            'trip_id' => Trip::factory(),
            'country_code' => $this->faker->countryCode(),
            'city_name' => $this->faker->city(),
            'visited_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
