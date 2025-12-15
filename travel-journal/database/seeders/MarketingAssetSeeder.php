<?php

namespace Database\Seeders;

use App\Models\MarketingAsset;
use Illuminate\Database\Seeder;

class MarketingAssetSeeder extends Seeder
{
    public function run(): void
    {
        $placeholderPath = 'images/placeholder.svg';

        $assets = [
            [
                'key' => 'world_map_wireframe',
                'type' => 'illustration',
                'label' => 'Blank Robinson projection map',
                'path' => $placeholderPath,
                'cdn_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/50/Robinson_projection_SW.jpg',
                'meta' => [
                    'alt' => 'Robinson projection base map with ocean shading',
                    'source' => 'Wikimedia Commons',
                    'source_url' => 'https://commons.wikimedia.org/wiki/File:Robinson_projection_SW.jpg',
                    'license' => 'Public domain',
                ],
            ],
            [
                'key' => 'trip_cover_default',
                'type' => 'cover',
                'label' => 'Lagoon approach in the Maldives',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80',
                'meta' => [
                    'alt' => 'Turquoise water and sand bars shot from above',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
                    'license' => 'Unsplash License',
                ],
            ],
            [
                'key' => 'trip_cover_sunset',
                'type' => 'cover',
                'label' => 'Golden dunes outside Abu Dhabi',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=1600&q=80',
                'meta' => [
                    'alt' => 'Orange desert dunes under sunset light',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d',
                    'license' => 'Unsplash License',
                ],
            ],
            [
                'key' => 'flag_pt',
                'type' => 'flag',
                'label' => 'Portugal flag',
                'path' => $placeholderPath,
                'cdn_url' => 'https://flagcdn.com/pt.svg',
                'meta' => [
                    'country' => 'PT',
                    'source' => 'FlagCDN',
                    'source_url' => 'https://flagcdn.com',
                ],
            ],
            [
                'key' => 'flag_jp',
                'type' => 'flag',
                'label' => 'Japan flag',
                'path' => $placeholderPath,
                'cdn_url' => 'https://flagcdn.com/jp.svg',
                'meta' => [
                    'country' => 'JP',
                    'source' => 'FlagCDN',
                    'source_url' => 'https://flagcdn.com',
                ],
            ],
            [
                'key' => 'flag_eg',
                'type' => 'flag',
                'label' => 'Egypt flag',
                'path' => $placeholderPath,
                'cdn_url' => 'https://flagcdn.com/eg.svg',
                'meta' => [
                    'country' => 'EG',
                    'source' => 'FlagCDN',
                    'source_url' => 'https://flagcdn.com',
                ],
            ],
            [
                'key' => 'flag_us',
                'type' => 'flag',
                'label' => 'United States flag',
                'path' => $placeholderPath,
                'cdn_url' => 'https://flagcdn.com/us.svg',
                'meta' => [
                    'country' => 'US',
                    'source' => 'FlagCDN',
                    'source_url' => 'https://flagcdn.com',
                ],
            ],
            [
                'key' => 'flag_mx',
                'type' => 'flag',
                'label' => 'Mexico flag',
                'path' => $placeholderPath,
                'cdn_url' => 'https://flagcdn.com/mx.svg',
                'meta' => [
                    'country' => 'MX',
                    'source' => 'FlagCDN',
                    'source_url' => 'https://flagcdn.com',
                ],
            ],
            [
                'key' => 'gallery_camera',
                'type' => 'gallery',
                'label' => 'Film camera on topo maps',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1458538977777-bfef44f0baff?auto=format&fit=crop&w=800&q=80',
                'meta' => [
                    'alt' => 'Vintage rangefinder resting on maps and notebooks',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1458538977777-bfef44f0baff',
                ],
            ],
            [
                'key' => 'gallery_journal',
                'type' => 'gallery',
                'label' => 'Field journal and watercolor set',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=800&q=80',
                'meta' => [
                    'alt' => 'Open notebook surrounded by brushes and pigment',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e',
                ],
            ],
            [
                'key' => 'gallery_mountain',
                'type' => 'gallery',
                'label' => 'Ridgeline over alpine valley',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5?auto=format&fit=crop&w=1200&q=80',
                'meta' => [
                    'alt' => 'Snow capped peaks under purple light',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1500534314209-b25b1d46c5b5',
                ],
            ],
            [
                'key' => 'avatar_one',
                'type' => 'avatar',
                'label' => 'Traveler portrait one',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=400&q=80',
                'meta' => [
                    'alt' => 'Portrait of traveler wearing backpack',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1',
                ],
            ],
            [
                'key' => 'avatar_two',
                'type' => 'avatar',
                'label' => 'Traveler portrait two',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
                'meta' => [
                    'alt' => 'Traveler smiling into sunset light',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                ],
            ],
            [
                'key' => 'avatar_three',
                'type' => 'avatar',
                'label' => 'Traveler portrait three',
                'path' => $placeholderPath,
                'cdn_url' => 'https://images.unsplash.com/photo-1525130413817-d45c1d127c42?auto=format&fit=crop&w=400&q=80',
                'meta' => [
                    'alt' => 'Traveler holding a camera',
                    'source' => 'Unsplash',
                    'source_url' => 'https://images.unsplash.com/photo-1525130413817-d45c1d127c42',
                ],
            ],
        ];

        foreach ($assets as $asset) {
            MarketingAsset::updateOrCreate(
                ['key' => $asset['key']],
                $asset
            );
        }
    }
}
