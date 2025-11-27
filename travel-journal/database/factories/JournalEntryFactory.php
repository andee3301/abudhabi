<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loggedAt = $this->faker->dateTimeBetween('-1 week', '+1 week');

        return [
            'trip_id' => Trip::factory(),
            'user_id' => fn (array $attributes) => Trip::find($attributes['trip_id'])->user_id ?? User::factory(),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->paragraphs(2, true),
            'location' => $this->faker->city(),
            'logged_at' => $loggedAt,
            'is_public' => $this->faker->boolean(30),
        ];
    }
}
