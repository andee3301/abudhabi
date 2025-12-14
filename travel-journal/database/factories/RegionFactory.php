<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country_code' => $this->faker->countryCode(),
            'code' => strtoupper($this->faker->lexify('??')),
            'name' => $this->faker->state(),
            'default_timezone' => $this->faker->timezone(),
        ];
    }
}
