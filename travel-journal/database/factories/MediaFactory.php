<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'disk' => 'public',
            'path' => 'journal/'.$this->faker->uuid().'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(80_000, 800_000),
            'width' => 1600,
            'height' => 900,
            'caption' => $this->faker->sentence(6),
        ];
    }
}
