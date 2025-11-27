<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
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
            'destination' => $this->faker->city(),
            'start_date' => $start,
            'end_date' => $end,
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed']),
            'notes' => $this->faker->boolean(70) ? $this->faker->paragraph() : null,
            'cover_image_path' => null,
            'timezone' => $this->faker->timezone(),
        ];
    }
}
