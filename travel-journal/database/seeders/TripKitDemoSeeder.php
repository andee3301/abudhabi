<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\JournalEntry;
use App\Models\Trip;
use App\Models\User;
use App\Models\UserHomeSetting;
use App\Models\WeatherSnapshot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TripKitDemoSeeder extends Seeder
{
    /**
     * Seed deterministic, offline-safe demo data.
     */
    public function run(): void
    {
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@treep.test'],
            [
                'name' => 'Ava Rivera',
                'display_name' => 'Ava',
                'home_country' => 'US',
                'avatar_url' => 'placeholders/avatar.jpg',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        UserHomeSetting::updateOrCreate(
            ['user_id' => $demoUser->id],
            [
                'home_country_code' => 'US',
                'home_timezone' => 'America/New_York',
                'preferred_currency' => 'USD',
            ]
        );

        $journeys = [
            ['title' => 'Tokyo Night Notes', 'city_slug' => 'tokyo', 'status' => 'ongoing', 'days' => 7, 'offset' => -2, 'mood' => '✨ Curious'],
            ['title' => 'London Studio Week', 'city_slug' => 'london', 'status' => 'planned', 'days' => 6, 'offset' => 10, 'mood' => '🌱 Calm'],
            ['title' => 'Bangkok Market Drift', 'city_slug' => 'bangkok', 'status' => 'completed', 'days' => 8, 'offset' => -30, 'mood' => '🌧 Reflective'],
            ['title' => 'New York Sketches', 'city_slug' => 'new-york', 'status' => 'ongoing', 'days' => 5, 'offset' => -1, 'mood' => '🧭 Adventurous'],
        ];

        foreach ($journeys as $journey) {
            $start = now()->addDays($journey['offset']);
            $end = (clone $start)->addDays($journey['days']);

            $city = City::firstOrCreate(
                ['slug' => $journey['city_slug']],
                [
                    'name' => Str::title(str_replace('-', ' ', $journey['city_slug'])),
                    'country_code' => Str::upper(substr($journey['city_slug'], 0, 2)),
                    'state_region' => null,
                    'timezone' => config('app.timezone'),
                    'currency_code' => 'USD',
                    'primary_language' => 'English',
                    'latitude' => null,
                    'longitude' => null,
                    'hero_image_url' => 'placeholders/city.jpg',
                ]
            );

            $trip = Trip::updateOrCreate(
                ['user_id' => $demoUser->id, 'title' => $journey['title']],
                [
                    'city_id' => $city->id,
                    'primary_location_name' => $city->display_name ?? $city->name,
                    'city' => $city->name,
                    'state_region' => $city->state_region,
                    'country_code' => $city->country_code,
                    'timezone' => $city->timezone,
                    'start_date' => $start,
                    'end_date' => $end,
                    'status' => $journey['status'],
                    'cover_image_url' => 'marketing/covers/atlas-blue.svg',
                    'tags' => [$journey['mood']],
                    'notes' => 'Offline demo itinerary seeded for Treep.',
                ]
            );

            JournalEntry::updateOrCreate(
                ['trip_id' => $trip->id, 'title' => $journey['title'].' — Arrival'],
                [
                    'user_id' => $demoUser->id,
                    'entry_date' => $start,
                    'body' => 'Settled in with offline maps and first impressions.',
                    'mood' => $journey['mood'],
                    'photo_urls' => ['placeholders/cover.jpg'],
                ]
            );

            JournalEntry::updateOrCreate(
                ['trip_id' => $trip->id, 'title' => $journey['title'].' — Highlights'],
                [
                    'user_id' => $demoUser->id,
                    'entry_date' => $end,
                    'body' => 'Captured moments and sketches to revisit later.',
                    'mood' => $journey['mood'],
                    'photo_urls' => ['placeholders/cover.jpg'],
                ]
            );

            WeatherSnapshot::updateOrCreate(
                [
                    'trip_id' => $trip->id,
                    'provider' => 'demo',
                    'recorded_at' => $start,
                ],
                [
                    'temperature' => 19,
                    'humidity' => 60,
                    'wind_speed' => 3.5,
                    'conditions' => 'Clear',
                    'icon' => 'demo-clear',
                    'payload' => ['note' => 'Static snapshot for offline demo'],
                ]
            );
        }
    }
}
