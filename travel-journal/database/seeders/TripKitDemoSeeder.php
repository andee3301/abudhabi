<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CountryVisit;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\JournalEntry;
use App\Models\Media;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Models\UserHomeSetting;
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
            'region' => 'Lisboa',
            'region_code' => 'LIS',
            'timezone' => 'Europe/Lisbon',
            'cities' => ['Lisbon', 'Sintra', 'Cascais'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Lisbon Field Notes',
            'narrative' => 'Azulejo sketches, tram bell alarms, and espresso-fueled writing sessions by the Tagus.',
        ],
        [
            'location' => 'Kyoto, Japan',
            'country_code' => 'JP',
            'region' => 'Kansai',
            'region_code' => 'KAN',
            'timezone' => 'Asia/Tokyo',
            'cities' => ['Kyoto', 'Arashiyama', 'Osaka'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Kyoto Tea Atlas',
            'narrative' => 'Pre-dawn temple walks chased by matcha tastings and notebook doodles of torii tunnels.',
        ],
        [
            'location' => 'Oaxaca, Mexico',
            'country_code' => 'MX',
            'region' => 'Oaxaca',
            'region_code' => 'OAX',
            'timezone' => 'America/Mexico_City',
            'cities' => ['Oaxaca City', 'Mitla', 'Puerto Escondido'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Oaxaca Story Chase',
            'narrative' => 'Markets heavy with color, mezcal tastings, and textile workshops under courtyard lights.',
        ],
        [
            'location' => 'Reykjavík, Iceland',
            'country_code' => 'IS',
            'region' => 'Capital Region',
            'region_code' => 'REK',
            'timezone' => 'Atlantic/Reykjavik',
            'cities' => ['Reykjavík', 'Vík', 'Akureyri'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Icelandic Light Diary',
            'narrative' => 'Lava field drives with geothermal stops, recording aurora sketches late into the night.',
        ],
        [
            'location' => 'Marrakech, Morocco',
            'country_code' => 'MA',
            'region' => 'Marrakech-Safi',
            'region_code' => 'MRK',
            'timezone' => 'Africa/Casablanca',
            'cities' => ['Marrakech', 'Essaouira', 'Atlas Mountains'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Marrakech Labyrinth',
            'narrative' => 'Riads, spice souks, rooftop lantern sketches, and early morning desert soundscapes.',
        ],
        [
            'location' => 'Cape Town, South Africa',
            'country_code' => 'ZA',
            'region' => 'Western Cape',
            'region_code' => 'CPT',
            'timezone' => 'Africa/Johannesburg',
            'cities' => ['Cape Town', 'Stellenbosch', 'Cederberg'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Cape Table Notebook',
            'narrative' => 'Table Mountain sunrises, penguin colony detours, and vineyard field recordings.',
        ],
        [
            'location' => 'Banff, Canada',
            'country_code' => 'CA',
            'region' => 'British Columbia',
            'region_code' => 'BC',
            'timezone' => 'America/Vancouver',
            'cities' => ['Banff', 'Lake Louise', 'Jasper'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Rocky Mountain Drafts',
            'narrative' => 'Alpine notebooks filled with turquoise lake studies and cabin fire playlists.',
        ],
        [
            'location' => 'Seoul, South Korea',
            'country_code' => 'KR',
            'region' => 'Seoul Capital Area',
            'region_code' => 'SEO',
            'timezone' => 'Asia/Seoul',
            'cities' => ['Seoul', 'Busan', 'Jeonju'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Seoul Night Markets',
            'narrative' => 'Cafés wired with synth playlists, gallery crawls, and neon reflections across the Han.',
        ],
        [
            'location' => 'Florence, Italy',
            'country_code' => 'IT',
            'region' => 'Tuscany',
            'region_code' => 'TOS',
            'timezone' => 'Europe/Rome',
            'cities' => ['Florence', 'Pisa', 'Siena'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Tuscan Sketch Circuit',
            'narrative' => 'Studios tucked behind Renaissance facades and late-night gelato debriefs.',
        ],
        [
            'location' => 'Queenstown, New Zealand',
            'country_code' => 'NZ',
            'region' => 'Otago',
            'region_code' => 'OTA',
            'timezone' => 'Pacific/Auckland',
            'cities' => ['Queenstown', 'Wanaka', 'Milford Sound'],
            'cover' => 'marketing/covers/atlas-sunset.svg',
            'title' => 'Southern Alps Circuit',
            'narrative' => 'Ridgeline hikes, glacier-fed lakes, and heli-journal sessions above the clouds.',
        ],
        [
            'location' => 'Buenos Aires, Argentina',
            'country_code' => 'AR',
            'region' => 'Buenos Aires Province',
            'region_code' => 'BUE',
            'timezone' => 'America/Argentina/Buenos_Aires',
            'cities' => ['Buenos Aires', 'Colonia', 'Mendoza'],
            'cover' => 'marketing/covers/atlas-blue.svg',
            'title' => 'Buenos Aires Rhythms',
            'narrative' => 'Tango hall field notes, leafy Palermo walks, and rooftop asado journaling.',
        ],
        [
            'location' => 'Jaipur, India',
            'country_code' => 'IN',
            'region' => 'Rajasthan',
            'region_code' => 'RJ',
            'timezone' => 'Asia/Kolkata',
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
            $this->ensureHomeSettings($user);
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

    protected function ensureHomeSettings(User $user): void
    {
        $homeCity = City::whereIn('slug', ['new-york', 'tokyo', 'london', 'paris'])
            ->inRandomOrder()
            ->first() ?? City::first();

        UserHomeSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'home_city_id' => $homeCity?->id,
                'home_city_name' => $homeCity?->name,
                'home_country_code' => $homeCity?->country_code,
                'home_timezone' => $homeCity?->timezone ?? 'UTC',
                'preferred_currency' => $homeCity?->currency_code ?? 'USD',
                'locale' => 'en',
            ]
        );
    }

    protected function createTripWithDetails(User $user, ?string $forcedStatus = null): void
    {
        $destination = Arr::random($this->destinations);
        $region = $this->resolveRegion($destination);
        $city = $this->resolveCity($destination);
        $status = $forcedStatus ?? Arr::random($this->statusDistribution);
        [$start, $end] = $this->dateWindowForStatus($status);

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
            'title' => $this->buildTripTitle($destination),
            'primary_location_name' => $destination['location'],
            'city_id' => $city?->id,
            'city' => $destination['cities'][0] ?? null,
            'state_region' => $region->name,
            'country_code' => $destination['country_code'],
            'timezone' => $destination['timezone'] ?? $region->default_timezone,
            'region_id' => $region->id,
            'start_date' => $start,
            'end_date' => $end,
            'status' => $status,
            'companion_name' => fake()->boolean(55) ? fake()->firstName() : null,
            'notes' => $this->narrativeFor($destination),
            'cover_image_url' => $destination['cover'],
            'tags' => [$destination['country_code'], $status, 'demo'],
        ]);

        $itinerary = $this->seedPrimaryItinerary($trip, $city, $start, $end);
        $this->seedItineraryItems($trip, $destination, $region, $city, $itinerary);
        $entries = $this->seedJournalEntries($trip, $user);
        $this->seedMediaForEntries($entries);
        $this->seedCountryVisits($trip, $destination, $region);
        $this->seedWeatherSnapshots($trip);
    }

    protected function seedPrimaryItinerary(Trip $trip, ?City $city, Carbon $start, Carbon $end): Itinerary
    {
        return Itinerary::create([
            'trip_id' => $trip->id,
            'city_id' => $city?->id,
            'title' => $trip->title.' Itinerary',
            'day_count' => max(1, $start->diffInDays($end) + 1),
            'start_date' => $start,
            'end_date' => $end,
            'is_primary' => true,
            'theme' => 'demo',
            'metadata' => ['seeded' => true],
        ]);
    }

    protected function seedItineraryItems(Trip $trip, array $destination, Region $region, ?City $city = null, ?Itinerary $itinerary = null): void
    {
        $count = random_int(6, 10);
        $seeder = $this;

        ItineraryItem::factory()
            ->count($count)
            ->state(function () use ($trip, $destination, $region, $city, $itinerary, $seeder) {
                $startWindow = Carbon::parse($trip->start_date)->subDays(1);
                $endWindow = Carbon::parse($trip->end_date)->addDays(1);
                $start = fake()->dateTimeBetween($startWindow, $endWindow);
                $dayNumber = Carbon::parse($trip->start_date)->diffInDays($start) + 1;
                $cityName = Arr::random($destination['cities']);
                $itemCity = $seeder->resolveCity($destination, $cityName);

                return [
                    'trip_id' => $trip->id,
                    'itinerary_id' => $itinerary?->id,
                    'city_id' => $itemCity?->id ?? $city?->id,
                    'region_id' => $region->id,
                    'country_code' => $destination['country_code'],
                    'state_region' => $region->name,
                    'city' => $cityName,
                    'timezone' => $destination['timezone'] ?? $region->default_timezone,
                    'start_datetime' => $start,
                    'end_datetime' => (clone $start)->modify('+'.random_int(2, 8).' hours'),
                    'location_name' => $cityName,
                    'day_number' => $dayNumber,
                    'sort_order' => ($dayNumber * 10) + random_int(1, 9),
                    'links' => ['map' => 'https://maps.example.test/'.Str::slug($cityName)],
                    'tags' => ['demo', strtolower($destination['country_code'])],
                ];
            })
            ->create();
    }

    protected function resolveCity(array $destination, ?string $cityName = null): City
    {
        $name = $cityName ?: ($destination['cities'][0] ?? $destination['location']);
        $slug = Str::slug($name.'-'.$destination['country_code']);

        return City::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'country_code' => $destination['country_code'],
                'state_region' => $destination['region'] ?? null,
                'timezone' => $destination['timezone'] ?? null,
                'currency_code' => Arr::get($destination, 'currency_code'),
                'primary_language' => Arr::get($destination, 'language'),
                'hero_image_url' => Arr::get($destination, 'cover'),
                'accent_color' => Arr::get($destination, 'accent_color'),
            ]
        );
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

    protected function seedCountryVisits(Trip $trip, array $destination, ?Region $primaryRegion = null): void
    {
        $count = random_int(2, 4);
        $seeder = $this;

        CountryVisit::factory()
            ->count($count)
            ->state(function () use ($trip, $destination, $primaryRegion, $seeder) {
                $usePrimary = fake()->boolean(75);
                $citySource = $usePrimary ? $destination : $seeder->randomDestination();
                $region = $usePrimary
                    ? ($primaryRegion ?? $seeder->resolveRegion($destination))
                    : $seeder->resolveRegion($citySource);

                return [
                    'trip_id' => $trip->id,
                    'country_code' => $citySource['country_code'],
                    'city_name' => Arr::random($citySource['cities']),
                    'state_region' => $region->name,
                    'timezone' => $citySource['timezone'] ?? $region->default_timezone,
                    'region_id' => $region->id,
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

    protected function resolveRegion(array $destination): Region
    {
        return Region::firstOrCreate(
            [
                'country_code' => $destination['country_code'],
                'code' => $destination['region_code'] ?? strtoupper(Str::limit($destination['region'] ?? $destination['country_code'], 3, '')),
            ],
            [
                'name' => $destination['region'] ?? $destination['location'],
                'default_timezone' => $destination['timezone'] ?? 'UTC',
            ]
        );
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
