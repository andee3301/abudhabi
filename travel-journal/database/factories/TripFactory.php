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

        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'primary_location_name' => $this->faker->city().', '.$this->faker->country(),
            'start_date' => $start,
            'end_date' => $end,
            'status' => $this->faker->randomElement(['planned', 'ongoing', 'completed']),
            'companion_name' => $this->faker->boolean(60) ? $this->faker->firstName() : null,
            'notes' => $this->faker->boolean(70) ? $this->faker->paragraphs(2, true) : null,
            'cover_image_url' => Arr::random([
                'marketing/covers/atlas-blue.svg',
                'marketing/covers/atlas-sunset.svg',
            ]),
        ];
    }
}
