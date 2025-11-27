<?php

namespace Database\Seeders;

use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Trip;
use App\Models\User;
use App\Models\WeatherSnapshot;
use Illuminate\Database\Seeder;

class TravelDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create a handful of users with trips, journal entries, media, and weather snapshots.
        User::factory(3)
            ->create()
            ->each(function (User $user) {
                Trip::factory()
                    ->count(3)
                    ->for($user)
                    ->create()
                    ->each(function (Trip $trip) use ($user) {
                        // Attach weather snapshots to the trip.
                        WeatherSnapshot::factory()
                            ->count(5)
                            ->for($trip)
                            ->create();

                        // Create journal entries and attach media.
                        JournalEntry::factory()
                            ->count(4)
                            ->for($trip)
                            ->for($user)
                            ->create()
                            ->each(function (JournalEntry $entry) {
                                Media::factory()
                                    ->count(random_int(0, 2))
                                    ->for($entry)
                                    ->create();
                            });
                    });
            });
    }
}
