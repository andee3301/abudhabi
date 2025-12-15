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
            ['email' => 'test@example.com'],
            [
                'name' => 'Ava Rivera',
                'display_name' => 'Ava',
                'home_country' => 'US',
                'avatar_url' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&w=600&q=80',
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

        $currencyByCountry = [
            'JP' => 'JPY',
            'GB' => 'GBP',
            'TH' => 'THB',
            'US' => 'USD',
            'PT' => 'EUR',
            'MA' => 'MAD',
            'ZA' => 'ZAR',
            'KR' => 'KRW',
            'IT' => 'EUR',
            'NZ' => 'NZD',
            'AR' => 'ARS',
            'IN' => 'INR',
            'IS' => 'ISK',
            'EG' => 'EGP',
        ];

        $languageByCountry = [
            'JP' => 'Japanese',
            'GB' => 'English',
            'TH' => 'Thai',
            'US' => 'English',
            'PT' => 'Portuguese',
            'MA' => 'Arabic',
            'ZA' => 'English',
            'KR' => 'Korean',
            'IT' => 'Italian',
            'NZ' => 'English',
            'AR' => 'Spanish',
            'IN' => 'Hindi',
            'IS' => 'Icelandic',
            'EG' => 'Arabic',
        ];

        $journeys = [
            [
                'title' => 'Kyoto Spring Dispatch',
                'city_slug' => 'kyoto',
                'status' => 'ongoing',
                'days' => 7,
                'offset' => -2,
                'mood' => '🧭 Adventurous',
                'cover_url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1500534310565-9386d0e0f1c5?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#ea580c',
                'city_summary' => 'Dawn temple bells, machiya lanes, and tea rooms fill this Kyoto sprint between cherry blossoms.',
                'notes' => 'Notebook covers Nishiki Market breakfast stops, JR rail legs, and okonomiyaki tastings recorded with a stereo mic.',
                'sources' => [
                    ['label' => 'Wikipedia — Kyoto', 'url' => 'https://en.wikipedia.org/wiki/Kyoto'],
                    ['label' => 'Unsplash — Kyoto laneways', 'url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Gion dusk sketches',
                        'day_offset' => 0,
                        'body' => 'Sketched lantern light across Hanamikoji Street while shamisen rehearsals floated from upstairs windows.',
                        'photos' => ['https://images.unsplash.com/photo-1491884662610-dfcd28f30cf6?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Arashiyama field audio',
                        'day_offset' => 3,
                        'body' => 'Recorded footsteps inside the bamboo grove before riding the Sagano Scenic Railway back toward the Kamo delta.',
                        'photos' => ['https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 18,
                    'humidity' => 58,
                    'wind_speed' => 2.4,
                    'conditions' => 'Partly Cloudy',
                    'icon' => '03d',
                    'day_offset' => 1,
                    'source' => 'Japan Meteorological Agency seasonal normals',
                ],
            ],
            [
                'title' => 'London Studio Week',
                'city_slug' => 'london',
                'status' => 'planned',
                'days' => 6,
                'offset' => 12,
                'mood' => '✨ Curious',
                'cover_url' => 'https://images.unsplash.com/photo-1471115853179-bb1d604434e0?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1469475714021-af0fddc2fe6a?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#4338ca',
                'city_summary' => 'A residency split between Tate research days and Thames-side field notes.',
                'notes' => 'Mapping Shoreditch studio visits, Barbican sound checks, and Jubilee line transfer windows.',
                'sources' => [
                    ['label' => 'Wikipedia — London', 'url' => 'https://en.wikipedia.org/wiki/London'],
                    ['label' => 'Unsplash — Westminster skyline', 'url' => 'https://images.unsplash.com/photo-1471115853179-bb1d604434e0'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Bankside site visits',
                        'day_offset' => 1,
                        'body' => 'Walked from Borough Market to Tate Modern logging facade textures and ambient Thames audio.',
                        'photos' => ['https://images.unsplash.com/photo-1505761671935-60b3a7427bad?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Soho interview loop',
                        'day_offset' => 3,
                        'body' => 'Met three illustrators at Greek Street, captured quotes for the residency zine, and pinned Piccadilly line timings.',
                        'photos' => ['https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 15,
                    'humidity' => 72,
                    'wind_speed' => 4.8,
                    'conditions' => 'Light Rain',
                    'icon' => '10d',
                    'day_offset' => 2,
                    'source' => 'Met Office outlook',
                ],
            ],
            [
                'title' => 'Bangkok Market Drift',
                'city_slug' => 'bangkok',
                'status' => 'completed',
                'days' => 9,
                'offset' => -45,
                'mood' => '🌧 Reflective',
                'cover_url' => 'https://images.unsplash.com/photo-1505761671935-60b3a7427bad?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1504595403659-9088ce801e29?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#f97316',
                'city_summary' => 'River ferries, floating markets, and midnight food courts documented across humid notebooks.',
                'notes' => 'Talat Noi textures turned into charcoal rubbings, while Khlong boats provided ambient audio for later mixing.',
                'sources' => [
                    ['label' => 'Wikipedia — Bangkok', 'url' => 'https://en.wikipedia.org/wiki/Bangkok'],
                    ['label' => 'Unsplash — Chao Phraya ferry', 'url' => 'https://images.unsplash.com/photo-1505761671935-60b3a7427bad'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Chao Phraya ferry lines',
                        'day_offset' => 0,
                        'body' => 'Charted express boats from Sathorn to Tha Tien while sketching Wat Arun silhouettes.',
                        'photos' => ['https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Talat Noi charcoal rubbings',
                        'day_offset' => 4,
                        'body' => 'Documented old engine blocks and typography for the zine, then logged tasting notes for kuih desserts.',
                        'photos' => ['https://images.unsplash.com/photo-1504595403659-9088ce801e29?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 31,
                    'humidity' => 78,
                    'wind_speed' => 3.1,
                    'conditions' => 'Humid',
                    'icon' => '01d',
                    'day_offset' => 2,
                    'source' => 'Thai Meteorological Department',
                ],
            ],
            [
                'title' => 'New York Sketchbook Sprint',
                'city_slug' => 'new-york',
                'status' => 'ongoing',
                'days' => 5,
                'offset' => 0,
                'mood' => '🧭 Adventurous',
                'cover_url' => 'https://images.unsplash.com/photo-1469475653889-6d2b8a5e0c4d?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#0f172a',
                'city_summary' => 'Sunrise laps on the Williamsburg Bridge and museum nights keep this sprint moving.',
                'notes' => 'M train sketches, Essex Market tastings, and Hudson River breeze measurements logged for a print essay.',
                'sources' => [
                    ['label' => 'Wikipedia — New York City', 'url' => 'https://en.wikipedia.org/wiki/New_York_City'],
                    ['label' => 'Unsplash — Lower Manhattan', 'url' => 'https://images.unsplash.com/photo-1469475653889-6d2b8a5e0c4d'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Brooklyn rooftop meteorology',
                        'day_offset' => 0,
                        'body' => 'Tracked cloud ceilings over Bushwick while exporting timelapses to the iPad storyboard.',
                        'photos' => ['https://images.unsplash.com/photo-1471043439891-5f6733c4ada4?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Chelsea notebook lab',
                        'day_offset' => 3,
                        'body' => 'Printed risograph proofs at the Center for Book Arts and catalogued gallery cards.',
                        'photos' => ['https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 22,
                    'humidity' => 55,
                    'wind_speed' => 5.2,
                    'conditions' => 'Breezy',
                    'icon' => '02d',
                    'day_offset' => 1,
                    'source' => 'NOAA Hudson observations',
                ],
            ],
            [
                'title' => 'Lisbon Atlantic Letters',
                'city_slug' => 'lisbon',
                'status' => 'completed',
                'days' => 8,
                'offset' => -110,
                'mood' => '🌧 Reflective',
                'cover_url' => 'https://images.unsplash.com/photo-1464790719320-516ecd75af6c?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1429041966141-44d228a42775?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#fbbf24',
                'city_summary' => 'Hilltop miradouros, tram bells, and Atlantic spray documented in deep blue inks.',
                'notes' => 'Catalogued azulejo patterns, LX Factory studio drop-ins, and Linha de Cascais rail cues.',
                'sources' => [
                    ['label' => 'Wikipedia — Lisbon', 'url' => 'https://en.wikipedia.org/wiki/Lisbon'],
                    ['label' => 'Unsplash — Alfama rooftops', 'url' => 'https://images.unsplash.com/photo-1464790719320-516ecd75af6c'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Rua da Bica sunrise',
                        'day_offset' => 1,
                        'body' => 'Followed Elevador da Bica as fog cleared over the Tagus basin; lettered type samples on-site.',
                        'photos' => ['https://images.unsplash.com/photo-1471115853179-bb1d604434e0?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Cascais tide log',
                        'day_offset' => 4,
                        'body' => 'Timed waves near Boca do Inferno and compared to hydrophone recordings for the essay audio bed.',
                        'photos' => ['https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 20,
                    'humidity' => 68,
                    'wind_speed' => 3.8,
                    'conditions' => 'Marine Layer',
                    'icon' => '50d',
                    'day_offset' => 2,
                    'source' => 'IPMA coastal bulletin',
                ],
            ],
            [
                'title' => 'Marrakech Atlas Prep',
                'city_slug' => 'marrakech',
                'status' => 'planned',
                'days' => 5,
                'offset' => 24,
                'mood' => '✨ Curious',
                'cover_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#f97316',
                'city_summary' => 'Riad rooftops and Atlas foothills framed for a textile residency.',
                'notes' => 'Souk palette studies, Jardin Majorelle cyan references, and High Atlas acclimation schedule all live in this plan.',
                'sources' => [
                    ['label' => 'Wikipedia — Marrakesh', 'url' => 'https://en.wikipedia.org/wiki/Marrakesh'],
                    ['label' => 'Unsplash — Medina sunset', 'url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Medina soundstage',
                        'day_offset' => 1,
                        'body' => 'Planned mic placements near Jemaa el-Fnaa storytellers to capture cadence without clipping.',
                        'photos' => ['https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Atlas gear pull',
                        'day_offset' => 3,
                        'body' => 'Laid out crampons, sketch rolls, and field recorders for an overnight refuge push.',
                        'photos' => ['https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 27,
                    'humidity' => 32,
                    'wind_speed' => 4.1,
                    'conditions' => 'Dry Heat',
                    'icon' => '01d',
                    'day_offset' => 2,
                    'source' => 'Moroccan Meteorological Directorate outlook',
                ],
            ],
            [
                'title' => 'Cape Table Notebook',
                'city_slug' => 'cape-town',
                'status' => 'completed',
                'days' => 12,
                'offset' => -150,
                'mood' => '🌧 Reflective',
                'cover_url' => 'https://images.unsplash.com/photo-1504893524553-b8553fbb1a33?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#0ea5e9',
                'city_summary' => 'Table Mountain fog studies, Bo-Kaap color fields, and False Bay swell logs.',
                'notes' => "Chapman's Peak drives recorded in binaural audio; winelands sketches bound into a stitched folio.",
                'sources' => [
                    ['label' => 'Wikipedia — Cape Town', 'url' => 'https://en.wikipedia.org/wiki/Cape_Town'],
                    ['label' => 'Unsplash — Table Mountain', 'url' => 'https://images.unsplash.com/photo-1504893524553-b8553fbb1a33'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Bo-Kaap palette grid',
                        'day_offset' => 2,
                        'body' => 'Logged paint codes from Chiappini Street doors and matched them against Pantone chips.',
                        'photos' => ['https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Cape Point swell watch',
                        'day_offset' => 6,
                        'body' => 'Tracked swell period at Diaz Beach and penciled cormorant patterns for the travelogue margins.',
                        'photos' => ['https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 19,
                    'humidity' => 70,
                    'wind_speed' => 7.2,
                    'conditions' => 'Coastal Wind',
                    'icon' => '50d',
                    'day_offset' => 4,
                    'source' => 'South African Weather Service marine bulletin',
                ],
            ],
            [
                'title' => 'Seoul Story Sprint',
                'city_slug' => 'seoul',
                'status' => 'ongoing',
                'days' => 4,
                'offset' => -5,
                'mood' => '🧭 Adventurous',
                'cover_url' => 'https://images.unsplash.com/photo-1471043439891-5f6733c4ada4?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#0ea5e9',
                'city_summary' => 'Neighborhood field recordings from Ikseon-dong to the Han River boardwalk.',
                'notes' => 'Seoul Metro journaling templates, kimchi fermentation interviews, and Hongdae live sessions documented.',
                'sources' => [
                    ['label' => 'Wikipedia — Seoul', 'url' => 'https://en.wikipedia.org/wiki/Seoul'],
                    ['label' => 'Unsplash — Seoul rooftops', 'url' => 'https://images.unsplash.com/photo-1471043439891-5f6733c4ada4'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Ikseon-dong courtyard audio',
                        'day_offset' => 0,
                        'body' => 'Captured cafe bustle between hanok courtyards and charted café wifi speeds.',
                        'photos' => ['https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Han River night ride',
                        'day_offset' => 2,
                        'body' => 'Rode rental bikes from Yeouido to Ttukseom measuring breeze shifts for the podcast intro.',
                        'photos' => ['https://images.unsplash.com/photo-1471043439891-5f6733c4ada4?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 23,
                    'humidity' => 61,
                    'wind_speed' => 3.9,
                    'conditions' => 'Clear',
                    'icon' => '01n',
                    'day_offset' => 1,
                    'source' => 'Korea Meteorological Administration',
                ],
            ],
            [
                'title' => 'Florence Sketch Circuit',
                'city_slug' => 'florence',
                'status' => 'planned',
                'days' => 7,
                'offset' => 32,
                'mood' => '🌱 Calm',
                'cover_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#f97316',
                'city_summary' => 'Seven-day lap through Renaissance studios and Arno riverbanks.',
                'notes' => 'Booked Uffizi dawn slots, Santa Croce paper suppliers, and Chianti day rides.',
                'sources' => [
                    ['label' => 'Wikipedia — Florence', 'url' => 'https://en.wikipedia.org/wiki/Florence'],
                    ['label' => 'Unsplash — Duomo overlook', 'url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Oltrarno studio crawl',
                        'day_offset' => 1,
                        'body' => 'Reserved bench time at a book-binding workshop and recorded pigment recipes.',
                        'photos' => ['https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Arno twilight ride',
                        'day_offset' => 4,
                        'body' => 'Plotted Viale Michelangelo climbs with audio cues for the docuseries.',
                        'photos' => ['https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1600&q=80&sat=-12'],
                    ],
                ],
                'weather' => [
                    'temperature' => 26,
                    'humidity' => 54,
                    'wind_speed' => 3.3,
                    'conditions' => 'Sunny',
                    'icon' => '01d',
                    'day_offset' => 2,
                    'source' => 'Italian Air Force Met Service',
                ],
            ],
            [
                'title' => 'Queenstown Alpine Notes',
                'city_slug' => 'queenstown',
                'status' => 'planned',
                'days' => 9,
                'offset' => 60,
                'mood' => '✨ Curious',
                'cover_url' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#0ea5e9',
                'city_summary' => 'Southern Alps scouting mission for winter routes and lake recordings.',
                'notes' => 'Pinned DOC hut availability, Shotover jet call sheet, and Remarkables avalanche refreshers.',
                'sources' => [
                    ['label' => 'Wikipedia — Queenstown, New Zealand', 'url' => 'https://en.wikipedia.org/wiki/Queenstown,_New_Zealand'],
                    ['label' => 'Unsplash — Lake Wakatipu', 'url' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Ben Lomond recce',
                        'day_offset' => 2,
                        'body' => 'Marked switchbacks and drone waypoints ahead of the alpine start.',
                        'photos' => ['https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Lakefront binaurals',
                        'day_offset' => 5,
                        'body' => 'Captured late-day wind shear sweeping across Wakatipu for the film score.',
                        'photos' => ['https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1600&q=80&sat=-15'],
                    ],
                ],
                'weather' => [
                    'temperature' => 12,
                    'humidity' => 65,
                    'wind_speed' => 6.1,
                    'conditions' => 'Mountain Gusts',
                    'icon' => '13d',
                    'day_offset' => 1,
                    'source' => 'MetService Southern Lakes outlook',
                ],
            ],
            [
                'title' => 'Buenos Aires Rhythms',
                'city_slug' => 'buenos-aires',
                'status' => 'completed',
                'days' => 6,
                'offset' => -80,
                'mood' => '🌧 Reflective',
                'cover_url' => 'https://images.unsplash.com/photo-1505843513577-22bb7d21e455?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1505843513577-22bb7d21e455?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#be123c',
                'city_summary' => 'Milonga nights, La Boca murals, and Palermo studio drop-ins filed for the radio hour.',
                'notes' => 'Tracked Subte delays, dulce de leche tastings, and pressings at Casa Brandon.',
                'sources' => [
                    ['label' => 'Wikipedia — Buenos Aires', 'url' => 'https://en.wikipedia.org/wiki/Buenos_Aires'],
                    ['label' => 'Unsplash — San Telmo rooftops', 'url' => 'https://images.unsplash.com/photo-1505843513577-22bb7d21e455'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'San Telmo antique crawl',
                        'day_offset' => 1,
                        'body' => 'Catalogued hand-painted signs and accordion riffs for the mixtape intro.',
                        'photos' => ['https://images.unsplash.com/photo-1505843513577-22bb7d21e455?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'La Boca color study',
                        'day_offset' => 3,
                        'body' => 'Laid down CMYK swatches inspired by Caminito and logged interviews with muralists.',
                        'photos' => ['https://images.unsplash.com/photo-1471043439891-5f6733c4ada4?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 24,
                    'humidity' => 64,
                    'wind_speed' => 4.4,
                    'conditions' => 'Humid',
                    'icon' => '02d',
                    'day_offset' => 2,
                    'source' => 'Servicio Meteorológico Nacional climatology',
                ],
            ],
            [
                'title' => 'Cairo Bazaar Notes',
                'city_slug' => 'cairo',
                'status' => 'completed',
                'days' => 7,
                'offset' => -120,
                'mood' => '🌧 Reflective',
                'cover_url' => 'https://images.unsplash.com/photo-1521292270410-a8c4451c3d0e?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1521292270410-a8c4451c3d0e?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#f97316',
                'city_summary' => 'Spice routes, felucca drifts, and limestone glow across the Nile floodplain fill this journal.',
                'notes' => 'Khan el-Khalili interviews, Egyptian Museum sketch slots, and metro timings recorded.',
                'sources' => [
                    ['label' => 'Wikipedia — Cairo', 'url' => 'https://en.wikipedia.org/wiki/Cairo'],
                    ['label' => 'Unsplash — Khan el-Khalili stalls', 'url' => 'https://images.unsplash.com/photo-1521292270410-a8c4451c3d0e'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Khan el-Khalili field mix',
                        'day_offset' => 1,
                        'body' => 'Layered oud riffs with vendors trading copper lamps while sketching spice mounds for the zine.',
                        'photos' => ['https://images.unsplash.com/photo-1521292270410-a8c4451c3d0e?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Felucca night readings',
                        'day_offset' => 4,
                        'body' => 'Drifted past Zamalek taking water temperature readings and recording narration for the radio hour.',
                        'photos' => ['https://images.unsplash.com/photo-1521292270410-a8c4451c3d0e?auto=format&fit=crop&w=1600&q=80&sat=-15'],
                    ],
                ],
                'weather' => [
                    'temperature' => 29,
                    'humidity' => 44,
                    'wind_speed' => 5.5,
                    'conditions' => 'Hazy',
                    'icon' => '50d',
                    'day_offset' => 2,
                    'source' => 'Egyptian Meteorological Authority outlook',
                ],
            ],
            [
                'title' => 'Jaipur Pink Pages',
                'city_slug' => 'jaipur',
                'status' => 'ongoing',
                'days' => 7,
                'offset' => -8,
                'mood' => '🧭 Adventurous',
                'cover_url' => 'https://images.unsplash.com/photo-1500534310680-6025ab09a4f9?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1500534310680-6025ab09a4f9?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#f97316',
                'city_summary' => 'Hawa Mahal breezes, block-print studios, and spice market palettes logged daily.',
                'notes' => 'Pink City gate measurements, Amer Fort sunrise climbs, and Man Sagar lake drone shots queued.',
                'sources' => [
                    ['label' => 'Wikipedia — Jaipur', 'url' => 'https://en.wikipedia.org/wiki/Jaipur'],
                    ['label' => 'Unsplash — Hawa Mahal facade', 'url' => 'https://images.unsplash.com/photo-1500534310680-6025ab09a4f9'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Johari Bazaar gradient study',
                        'day_offset' => 1,
                        'body' => 'Tracked sari color blocks each hour and sampled local chai spices for the radio log.',
                        'photos' => ['https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Amber Fort ridge walk',
                        'day_offset' => 4,
                        'body' => 'Marked rampart switchbacks and measured peacock calls bouncing across the valley.',
                        'photos' => ['https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 34,
                    'humidity' => 45,
                    'wind_speed' => 2.9,
                    'conditions' => 'Hot',
                    'icon' => '01d',
                    'day_offset' => 2,
                    'source' => 'India Meteorological Department daily bulletin',
                ],
            ],
            [
                'title' => 'Reykjavik Light Diary',
                'city_slug' => 'reykjavik',
                'status' => 'planned',
                'days' => 5,
                'offset' => 40,
                'mood' => '🌱 Calm',
                'cover_url' => 'https://images.unsplash.com/photo-1489493512598-d08130d08de2?auto=format&fit=crop&w=1600&q=80',
                'hero_image_url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=2000&q=80',
                'accent_color' => '#38bdf8',
                'city_summary' => 'Aurora watch, geothermal pools, and midnight sun logs scheduled for midsummer.',
                'notes' => 'Blue Lagoon hydrophone tests, Harpa reverb study, and Golden Circle drone windows documented.',
                'sources' => [
                    ['label' => 'Wikipedia — Reykjavík', 'url' => 'https://en.wikipedia.org/wiki/Reykjav%C3%ADk'],
                    ['label' => 'Unsplash — Hallgrímskirkja aerial', 'url' => 'https://images.unsplash.com/photo-1489493512598-d08130d08de2'],
                ],
                'journal_entries' => [
                    [
                        'title' => 'Sundlaug logbook prep',
                        'day_offset' => 1,
                        'body' => 'Planned pool circuit between Sundhöllin and Vesturbæjarlaug with temperature readings.',
                        'photos' => ['https://images.unsplash.com/photo-1489493512598-d08130d08de2?auto=format&fit=crop&w=1600&q=80'],
                    ],
                    [
                        'title' => 'Harpa acoustic sweep',
                        'day_offset' => 3,
                        'body' => "Scheduled sine sweep recordings inside Harpa's main hall for the installation soundtrack.",
                        'photos' => ['https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1600&q=80'],
                    ],
                ],
                'weather' => [
                    'temperature' => 9,
                    'humidity' => 82,
                    'wind_speed' => 8.6,
                    'conditions' => 'Coastal Wind',
                    'icon' => '04d',
                    'day_offset' => 2,
                    'source' => 'Icelandic Met Office outlook',
                ],
            ],
        ];

        $cityData = collect(json_decode(file_get_contents(database_path('data/cities.json')), true) ?? []);

        foreach ($journeys as $journey) {
            $start = now()->addDays($journey['offset']);
            $end = (clone $start)->addDays($journey['days']);
            $cityName = Str::title(str_replace('-', ' ', $journey['city_slug']));
            $cityDetails = $cityData->first(function (array $attributes) use ($cityName) {
                $haystack = Str::lower($attributes['city']);
                $needle = Str::lower($cityName);

                return $haystack === $needle
                    || str_contains($haystack, $needle)
                    || str_contains($needle, $haystack);
            });

            $countryCode = strtoupper($cityDetails['country_code'] ?? $journey['country_code'] ?? Str::substr(Str::upper($journey['city_slug']), 0, 2));
            $currency = $journey['currency'] ?? $currencyByCountry[$countryCode] ?? 'USD';
            $language = $journey['language'] ?? $languageByCountry[$countryCode] ?? 'English';

            $city = City::updateOrCreate(
                ['slug' => $journey['city_slug']],
                [
                    'name' => $cityName,
                    'country_code' => $countryCode,
                    'state_region' => $cityDetails['state_region'] ?? null,
                    'timezone' => $cityDetails['timezone'] ?? config('app.timezone'),
                    'currency_code' => $currency,
                    'primary_language' => $language,
                    'latitude' => $cityDetails['lat'] ?? null,
                    'longitude' => $cityDetails['lng'] ?? null,
                    'hero_image_url' => $journey['hero_image_url'],
                    'accent_color' => $journey['accent_color'] ?? null,
                    'meta' => [
                        'summary' => $journey['city_summary'] ?? $cityDetails['summary'] ?? 'Documented for the offline demo.',
                        'sources' => $journey['sources'] ?? [],
                    ],
                ]
            );

            $trip = Trip::updateOrCreate(
                ['user_id' => $demoUser->id, 'title' => $journey['title']],
                [
                    'city_id' => $city->id,
                    'primary_location_name' => $journey['primary_location_name'] ?? $city->display_name ?? $city->name,
                    'city' => $city->name,
                    'state_region' => $city->state_region,
                    'country_code' => $city->country_code,
                    'timezone' => $city->timezone,
                    'start_date' => $start,
                    'end_date' => $end,
                    'status' => $journey['status'],
                    'cover_image_url' => $journey['cover_url'],
                    'tags' => [$journey['mood']],
                    'notes' => $journey['notes'],
                ]
            );

            foreach ($journey['journal_entries'] as $entry) {
                $entryDate = (clone $start)->addDays($entry['day_offset'] ?? 0);

                JournalEntry::updateOrCreate(
                    ['trip_id' => $trip->id, 'title' => $entry['title']],
                    [
                        'user_id' => $demoUser->id,
                        'entry_date' => $entryDate,
                        'body' => $entry['body'],
                        'mood' => $entry['mood'] ?? $journey['mood'],
                        'photo_urls' => $entry['photos'] ?? [],
                    ]
                );
            }

            if (! empty($journey['weather'])) {
                $weather = $journey['weather'];
                $recordedAt = (clone $start)->addDays($weather['day_offset'] ?? 0);

                WeatherSnapshot::updateOrCreate(
                    [
                        'trip_id' => $trip->id,
                        'provider' => 'demo',
                        'recorded_at' => $recordedAt,
                    ],
                    [
                        'temperature' => $weather['temperature'],
                        'humidity' => $weather['humidity'],
                        'wind_speed' => $weather['wind_speed'],
                        'conditions' => $weather['conditions'],
                        'icon' => $weather['icon'],
                        'payload' => [
                            'source' => $weather['source'] ?? 'Climatology reference',
                            'notes' => $weather['notes'] ?? null,
                        ],
                    ]
                );
            }
        }
    }
}
