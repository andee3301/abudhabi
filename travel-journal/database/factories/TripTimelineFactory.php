<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\TripTimeline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripTimeline>
 */
class TripTimelineFactory extends Factory
{
    protected $model = TripTimeline::class;

    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'occurred_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'type' => $this->faker->randomElement(['moment', 'flight', 'food', 'note', null]),
            'location_name' => $this->faker->optional()->city(),
            'tags' => [],
            'metadata' => [],
        ];
    }
}
