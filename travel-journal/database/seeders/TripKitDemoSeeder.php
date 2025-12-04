<?php

namespace Database\Seeders;

use App\Models\CountryVisit;
use App\Models\ItineraryItem;
use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Trip;
use App\Models\User;
use App\Models\WeatherSnapshot;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripKitDemoSeeder extends Seeder
{
    protected array $destinations = [
        [
            'location' => 'Lisbon, Portugal',
            'country_code' => 'PT',
            'cities' => ['Lisbon', 'Sintra', 'Cascais'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Lisbon Field Notes',
            'narrative' => 'Azulejo sketches, tram bell alarms, and espresso-fueled writing sessions by the Tagus.',
        ],
        [
            'location' => 'Kyoto, Japan',
            'country_code' => 'JP',
            'cities' => ['Kyoto', 'Arashiyama', 'Osaka'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Kyoto Tea Atlas',
            'narrative' => 'Pre-dawn temple walks chased by matcha tastings and notebook doodles of torii tunnels.',
        ],
        [
            'location' => 'Oaxaca, Mexico',
            'country_code' => 'MX',
            'cities' => ['Oaxaca City', 'Mitla', 'Puerto Escondido'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Oaxaca Story Chase',
            'narrative' => 'Markets heavy with color, mezcal tastings, and textile workshops under courtyard lights.',
        ],
        [
            'location' => 'Reykjavík, Iceland',
            'country_code' => 'IS',
            'cities' => ['Reykjavík', 'Vík', 'Akureyri'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Icelandic Light Diary',
            'narrative' => 'Lava field drives with geothermal stops, recording aurora sketches late into the night.',
        ],
        [
            'location' => 'Marrakech, Morocco',
            'country_code' => 'MA',
            'cities' => ['Marrakech', 'Essaouira', 'Atlas Mountains'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Marrakech Labyrinth',
            'narrative' => 'Riads, spice souks, rooftop lantern sketches, and early morning desert soundscapes.',
        ],
        [
            'location' => 'Cape Town, South Africa',
            'country_code' => 'ZA',
            'cities' => ['Cape Town', 'Stellenbosch', 'Cederberg'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Cape Table Notebook',
            'narrative' => 'Table Mountain sunrises, penguin colony detours, and vineyard field recordings.',
        ],
        [
            'location' => 'Banff, Canada',
            'country_code' => 'CA',
            'cities' => ['Banff', 'Lake Louise', 'Jasper'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Rocky Mountain Drafts',
            'narrative' => 'Alpine notebooks filled with turquoise lake studies and cabin fire playlists.',
        ],
        [
            'location' => 'Seoul, South Korea',
            'country_code' => 'KR',
            'cities' => ['Seoul', 'Busan', 'Jeonju'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Seoul Night Markets',
            'narrative' => 'Cafés wired with synth playlists, gallery crawls, and neon reflections across the Han.',
        ],
        [
            'location' => 'Florence, Italy',
            'country_code' => 'IT',
            'cities' => ['Florence', 'Pisa', 'Siena'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Tuscan Sketch Circuit',
            'narrative' => 'Studios tucked behind Renaissance facades and late-night gelato debriefs.',
        ],
        [
            'location' => 'Queenstown, New Zealand',
            'country_code' => 'NZ',
            'cities' => ['Queenstown', 'Wanaka', 'Milford Sound'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Southern Alps Circuit',
            'narrative' => 'Ridgeline hikes, glacier-fed lakes, and heli-journal sessions above the clouds.',
        ],
        [
            'location' => 'Buenos Aires, Argentina',
            'country_code' => 'AR',
            'cities' => ['Buenos Aires', 'Colonia', 'Mendoza'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Buenos Aires Rhythms',
            'narrative' => 'Tango hall field notes, leafy Palermo walks, and rooftop asado journaling.',
        ],
        [
            'location' => 'Jaipur, India',
            'country_code' => 'IN',
            'cities' => ['Jaipur', 'Jodhpur', 'Udaipur'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Pink City Palette',
            'narrative' => 'Block print ateliers, stepwell echoes, and spice market color studies.',
        ],
    ];

    protected array $galleryPool = [
        'marketing/gallery/camera.svg',
        'marketing/gallery/journal.svg',
        'marketing/gallery/mountain.svg',
        'marketing/avatars/one.svg',
        'marketing/avatars/two.svg',
        'marketing/avatars/three.svg',
        'marketing/flags/pt.svg',
        'marketing/flags/jp.svg',
        'marketing/flags/eg.svg',
        'marketing/flags/us.svg',
        'marketing/flags/mx.svg',
        'marketing/world-map.svg',
    ];

    protected array $tripDescriptors = [
        'Night Markets',
        'Residency',
        'Field Guide',
        'Slow Travel',
        'Trail Notes',
        'Studio Week',
        'Story Sprint',
    ];

    protected array $statusDistribution = ['planned', 'planned', 'ongoing', 'ongoing', 'completed', 'completed', 'completed'];

    protected array $weatherConditions = ['Clear', 'Clouds', 'Rain', 'Snow', 'Fog', 'Drizzle', 'Wind', 'Thunderstorms'];

    public function run(): void
    {
        $users = User::factory()->count(3)->create();

        $demoUser = User::factory()->create([
            'name' => 'Ava Rivera',
            'display_name' => 'Ava',
            'home_country' => 'US',
            'avatar_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=400&q=80',
            'email' => 'demo@tripkit.test',
            'password' => 'password',
        ]);

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'display_name' => 'Test',
            'home_country' => 'US',
            'avatar_url' => null,
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $allUsers = $users->concat([$demoUser, $testUser]);

        foreach ($allUsers as $user) {
            $forcedStatuses = $user->is($demoUser) ? ['ongoing', 'planned', 'completed'] : [];
            $this->seedTripsForUser($user, $forcedStatuses);
        }
    }

    protected function seedTripsForUser(User $user, array $forcedStatuses = []): void
    {
        $tripTotal = max(count($forcedStatuses), random_int(8, 12));

        for ($i = 0; $i < $tripTotal; $i++) {
            $status = $forcedStatuses[$i] ?? null;
            $this->createTripWithDetails($user, $status);
        }
    }

    protected function createTripWithDetails(User $user, ?string $forcedStatus = null): void
    {
        $destination = Arr::random($this->destinations);
        $status = $forcedStatus ?? Arr::random($this->statusDistribution);
        [$start, $end] = $this->dateWindowForStatus($status);

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'title' => $this->buildTripTitle($destination),
            'primary_location_name' => $destination['location'],
            'start_date' => $start,
            'end_date' => $end,
            'status' => $status,
            'companion_name' => fake()->boolean(55) ? fake()->firstName() : null,
            'notes' => $this->narrativeFor($destination),
            'cover_image_url' => $destination['cover'],
        ]);

        $this->seedItineraryItems($trip, $destination);
        $entries = $this->seedJournalEntries($trip, $user);
        $this->seedMediaForEntries($entries);
        $this->seedCountryVisits($trip, $destination);
        $this->seedWeatherSnapshots($trip);
    }

    protected function seedItineraryItems(Trip $trip, array $destination): void
    {
        $count = random_int(6, 10);

        ItineraryItem::factory()
            ->count($count)
            ->state(function () use ($trip, $destination) {
                $startWindow = Carbon::parse($trip->start_date)->subDays(1);
                $endWindow = Carbon::parse($trip->end_date)->addDays(1);
                $start = fake()->dateTimeBetween($startWindow, $endWindow);

                return [
                    'trip_id' => $trip->id,
                    'start_datetime' => $start,
                    'end_datetime' => (clone $start)->modify('+'.random_int(2, 8).' hours'),
                    'location_name' => Arr::random($destination['cities']),
                ];
            })
            ->create();
    }

    protected function seedJournalEntries(Trip $trip, User $user): EloquentCollection
    {
        $count = random_int(5, 9);
        $seeder = $this;

        return JournalEntry::factory()
            ->count($count)
            ->state(function () use ($trip, $user, $seeder) {
                $startWindow = Carbon::parse($trip->start_date)->subDays(2);
                $endWindow = Carbon::parse($trip->end_date)->addDays(2);
                $date = fake()->dateTimeBetween($startWindow, $endWindow);

                return [
                    'trip_id' => $trip->id,
                    'user_id' => $user->id,
                    'entry_date' => $date,
                    'photo_urls' => $seeder->randomPhotoSet(),
                ];
            })
            ->create();
    }

    protected function seedMediaForEntries(EloquentCollection $entries): void
    {
        $entries->each(function (JournalEntry $entry): void {
            Media::factory()
                ->count(random_int(1, 3))
                ->state(function () use ($entry) {
                    return [
                        'journal_entry_id' => $entry->id,
                        'path' => 'journal/'.$entry->id.'/'.Str::uuid().'.jpg',
                        'caption' => fake()->sentence(),
                    ];
                })
                ->create();
        });
    }

    protected function seedCountryVisits(Trip $trip, array $destination): void
    {
        $count = random_int(2, 4);
        $seeder = $this;

        CountryVisit::factory()
            ->count($count)
            ->state(function () use ($trip, $destination, $seeder) {
                $usePrimary = fake()->boolean(75);
                $citySource = $usePrimary ? $destination : $seeder->randomDestination();

                return [
                    'trip_id' => $trip->id,
                    'country_code' => $citySource['country_code'],
                    'city_name' => Arr::random($citySource['cities']),
                    'visited_at' => fake()->dateTimeBetween($trip->start_date, $trip->end_date),
                ];
            })
            ->create();
    }

    protected function seedWeatherSnapshots(Trip $trip): void
    {
        $seeder = $this;

        WeatherSnapshot::factory()
            ->count(random_int(4, 6))
            ->state(function () use ($trip, $seeder) {
                $startWindow = Carbon::parse($trip->start_date)->subDays(1);
                $endWindow = Carbon::parse($trip->end_date)->addDays(1);
                return [
                    'trip_id' => $trip->id,
                    'recorded_at' => fake()->dateTimeBetween($startWindow, $endWindow),
                    'conditions' => Arr::random($seeder->weatherConditions),
                    'icon' => Arr::random(['01d', '02d', '03d', '10d', '11d', '13d', '50d']),
                ];
            })
            ->create();
    }

    protected function buildTripTitle(array $destination): string
    {
        $descriptor = Arr::random($this->tripDescriptors);

        return $destination['title'].' · '.$descriptor;
    }

    protected function narrativeFor(array $destination): string
    {
        return $destination['narrative'].' '.fake()->sentences(2, true);
    }

    /**
     * @return array{0: \Carbon\CarbonInterface, 1: \Carbon\CarbonInterface}
     */
    protected function dateWindowForStatus(string $status): array
    {
        $duration = random_int(4, 14);

        if ($status === 'planned') {
            $start = now()->copy()->addDays(random_int(7, 80));
        } elseif ($status === 'ongoing') {
            $start = now()->copy()->subDays(random_int(1, 4));
        } else {
            $start = now()->copy()->subDays(random_int(35, 200));
        }

        $end = (clone $start)->addDays($duration);

        if ($status === 'ongoing') {
            $end = now()->copy()->addDays(random_int(3, 10));
        }

        return [$start, $end];
    }

    protected function randomPhotoSet(): array
    {
        $count = random_int(2, 3);
        $selection = Arr::random($this->galleryPool, $count);

        if (! is_array($selection)) {
            return [$selection];
        }

        return array_values($selection);
    }

    protected function randomDestination(): array
    {
        return Arr::random($this->destinations);
    }
}
