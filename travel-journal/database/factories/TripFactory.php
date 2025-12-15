<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+2 months');
        $end = (clone $start)->modify('+'.random_int(3, 14).' days');
        $stateRegion = $this->faker->state();
        $country = $this->faker->countryCode();
        $timezone = $this->faker->timezone();

        return [
            'user_id' => \App\Models\User::factory(),
            'city_id' => \App\Models\City::factory()->state([
                'country_code' => $country,
                'state_region' => $stateRegion,
                'timezone' => $timezone,
            ]),
            'title' => $this->faker->sentence(3),
            'primary_location_name' => $this->faker->city().', '.$this->faker->country(),
            'city' => $this->faker->city(),
            'state_region' => $stateRegion,
            'country_code' => $country,
            'timezone' => $timezone,
            'region_id' => \App\Models\Region::factory()->state([
                'country_code' => $country,
                'name' => $stateRegion,
                'default_timezone' => $timezone,
            ]),
            'start_date' => $start,
            'end_date' => $end,
            'status' => $this->faker->randomElement(['planned', 'ongoing', 'completed']),
            'companion_name' => $this->faker->boolean(60) ? $this->faker->firstName() : null,
            'notes' => $this->faker->boolean(70) ? $this->faker->paragraphs(2, true) : null,
            'cover_image_url' => Arr::random([
                'https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5?auto=format&fit=crop&w=1400&q=80',
                'https://images.unsplash.com/photo-1500534310680-6025ab09a4f9?auto=format&fit=crop&w=1400&q=80',
                'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1400&q=80',
            ]),
            'tags' => [$country, Str::slug($stateRegion)],
        ];
    }
}
