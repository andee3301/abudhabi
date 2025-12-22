<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\TripEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripEventFactory extends Factory
{
    protected $model = TripEvent::class;

    public function definition(): array
    {
        $types = ['location', 'hotel', 'travel', 'note'];
        $type = $this->faker->randomElement($types);

        return [
            'trip_id' => Trip::factory(),
            'type' => $type,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->boolean(70) ? $this->faker->paragraph() : null,
            'start_time' => $this->faker->dateTimeBetween('-2 days', '+5 days'),
            'end_time' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('+1 hours', '+6 days') : null,
            'location_data' => [
                'name' => $this->faker->city(),
                'address' => $this->faker->address(),
                'lat' => $this->faker->latitude(),
                'lng' => $this->faker->longitude(),
            ],
            'travel_method' => $type === 'travel' ? $this->faker->randomElement(['plane', 'train', 'car', 'boat']) : null,
            'media_path' => null,
            'position' => $this->faker->numberBetween(0, 50),
        ];
    }
}
