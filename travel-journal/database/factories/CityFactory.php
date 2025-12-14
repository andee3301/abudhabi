<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->city();
        $country = $this->faker->countryCode();
        $state = $this->faker->state();

        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.$country.'-'.$this->faker->unique()->randomNumber(4)),
            'country_code' => $country,
            'state_region' => $state,
            'timezone' => $this->faker->timezone(),
            'currency_code' => $this->faker->currencyCode(),
            'primary_language' => $this->faker->languageCode(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'hero_image_url' => null,
            'accent_color' => '#0ea5e9',
        ];
    }
}
