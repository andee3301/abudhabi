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
        $entryDate = $this->faker->dateTimeBetween('-2 months', 'now');

        return [
            'trip_id' => Trip::factory(),
            'user_id' => fn (array $attributes) => Trip::find($attributes['trip_id'])->user_id ?? User::factory(),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->paragraphs(3, true),
            'entry_date' => $entryDate,
            'mood' => $this->faker->randomElement(['joyful', 'curious', 'tired', 'inspired']),
            'photo_urls' => [
                'https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5?auto=format&fit=crop&w=1400&q=80',
            ],
        ];
    }
}
