<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\TripEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TripEventSeeder extends Seeder
{
    /**
     * Seed timeline activity for existing trips.
     */
    public function run(): void
    {
        $trips = Trip::all();

        foreach ($trips as $trip) {
            if ($trip->events()->exists()) {
                continue;
            }

            $baseTitle = $trip->primary_location_name ?: $trip->title;
            $start = $trip->start_date ? Carbon::parse($trip->start_date) : now()->subDays(2);
            $end = $trip->end_date ? Carbon::parse($trip->end_date) : now()->addDays(4);

            if ($end->lessThan($start)) {
                $end = $start->copy()->addDays(4);
            }

            $dayCount = max(1, $start->diffInDays($end) + 1);
            $templates = [
                [
                    'type' => 'travel',
                    'title' => 'Arrive in '.$baseTitle,
                    'description' => 'Flight, transfer, and arrival notes.',
                    'travel_method' => 'plane',
                    'location_data' => ['address' => $trip->primary_location_name],
                ],
                [
                    'type' => 'location',
                    'title' => 'Morning walk in '.$baseTitle,
                    'description' => 'Early walk and coffee spots.',
                    'location_data' => ['address' => $trip->location_label],
                ],
                [
                    'type' => 'hotel',
                    'title' => 'Hotel check-in',
                    'description' => 'Drop bags, confirm bookings, and rest.',
                    'location_data' => ['address' => $trip->location_label],
                ],
                [
                    'type' => 'location',
                    'title' => 'Market stroll',
                    'description' => 'Browse stalls and grab snacks.',
                    'location_data' => ['address' => $trip->location_label],
                ],
                [
                    'type' => 'note',
                    'title' => 'Local notes',
                    'description' => 'Quick notes and reminders.',
                    'location_data' => null,
                ],
            ];

            $count = min(6, max(3, $dayCount));
            $position = 1;

            for ($i = 0; $i < $count; $i++) {
                $template = $templates[$i % count($templates)];
                $dayOffset = min($i, $dayCount - 1);
                $startTime = $start->copy()->addDays($dayOffset)->setTime(9 + (($i % 4) * 2), 0);

                TripEvent::create([
                    'trip_id' => $trip->id,
                    'type' => $template['type'],
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'start_time' => $startTime,
                    'end_time' => $template['type'] === 'note' ? null : $startTime->copy()->addHours(2),
                    'location_data' => $template['location_data'],
                    'travel_method' => $template['travel_method'] ?? null,
                    'position' => $position++,
                ]);
            }
        }
    }
}
