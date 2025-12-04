<?php

namespace Database\Seeders;

use App\Models\MarketingAsset;
use Illuminate\Database\Seeder;

class MarketingAssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            [
                'key' => 'world_map_wireframe',
                'type' => 'illustration',
                'label' => 'World map wireframe',
                'path' => 'marketing/world-map.svg',
                'cdn_url' => 'https://upload.wikimedia.org/wikipedia/commons/8/80/World_map_-_low_resolution.svg',
                'meta' => [
                    'alt' => 'World map wireframe',
                    'source' => 'Wikimedia Commons (CC BY-SA 3.0)',
                ],
            ],
            [
                'key' => 'trip_cover_default',
                'type' => 'cover',
                'label' => 'Abstract atlas blue cover',
                'path' => 'marketing/covers/atlas-blue.svg',
                'meta' => [
                    'alt' => 'Abstract blue travel collage',
                ],
            ],
            [
                'key' => 'trip_cover_sunset',
                'type' => 'cover',
                'label' => 'Abstract atlas sunset cover',
                'path' => 'marketing/covers/atlas-sunset.svg',
                'meta' => [
                    'alt' => 'Abstract sunset travel collage',
                ],
            ],
            [
                'key' => 'flag_pt',
                'type' => 'flag',
                'label' => 'Portugal flag',
                'path' => 'marketing/flags/pt.svg',
                'cdn_url' => 'https://flagcdn.com/w40/pt.png',
                'meta' => [
                    'country' => 'PT',
                    'source' => 'FlagCDN (CC BY-SA 4.0)',
                ],
            ],
            [
                'key' => 'flag_jp',
                'type' => 'flag',
                'label' => 'Japan flag',
                'path' => 'marketing/flags/jp.svg',
                'cdn_url' => 'https://flagcdn.com/w40/jp.png',
                'meta' => [
                    'country' => 'JP',
                    'source' => 'FlagCDN (CC BY-SA 4.0)',
                ],
            ],
            [
                'key' => 'flag_eg',
                'type' => 'flag',
                'label' => 'Egypt flag',
                'path' => 'marketing/flags/eg.svg',
                'cdn_url' => 'https://flagcdn.com/w40/eg.png',
                'meta' => [
                    'country' => 'EG',
                    'source' => 'FlagCDN (CC BY-SA 4.0)',
                ],
            ],
            [
                'key' => 'flag_us',
                'type' => 'flag',
                'label' => 'United States flag',
                'path' => 'marketing/flags/us.svg',
                'cdn_url' => 'https://flagcdn.com/w40/us.png',
                'meta' => [
                    'country' => 'US',
                    'source' => 'FlagCDN (CC BY-SA 4.0)',
                ],
            ],
            [
                'key' => 'flag_mx',
                'type' => 'flag',
                'label' => 'Mexico flag',
                'path' => 'marketing/flags/mx.svg',
                'cdn_url' => 'https://flagcdn.com/w40/mx.png',
                'meta' => [
                    'country' => 'MX',
                    'source' => 'FlagCDN (CC BY-SA 4.0)',
                ],
            ],
            [
                'key' => 'gallery_camera',
                'type' => 'gallery',
                'label' => 'Camera illustration',
                'path' => 'marketing/gallery/camera.svg',
                'cdn_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=400&q=80',
                'meta' => [
                    'alt' => 'Illustration of a travel camera',
                    'source' => 'Unsplash / Vinta Supply Co.',
                ],
            ],
            [
                'key' => 'gallery_journal',
                'type' => 'gallery',
                'label' => 'Journal illustration',
                'path' => 'marketing/gallery/journal.svg',
                'cdn_url' => 'https://images.unsplash.com/photo-1483683804023-6ccdb62f86ef?auto=format&fit=crop&w=400&q=80',
                'meta' => [
                    'alt' => 'Illustration of a traveler writing',
                    'source' => 'Unsplash / Rawpixel',
                ],
            ],
            [
                'key' => 'gallery_mountain',
                'type' => 'gallery',
                'label' => 'Mountain illustration',
                'path' => 'marketing/gallery/mountain.svg',
                'cdn_url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=400&q=80',
                'meta' => [
                    'alt' => 'Illustration of a mountain sunset',
                    'source' => 'Unsplash / Nathan Anderson',
                ],
            ],
            [
                'key' => 'avatar_one',
                'type' => 'avatar',
                'label' => 'Avatar one',
                'path' => 'marketing/avatars/one.svg',
                'cdn_url' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&w=200&q=80',
                'meta' => [
                    'alt' => 'Traveler avatar illustration',
                    'source' => 'Unsplash / Brooke Cagle',
                ],
            ],
            [
                'key' => 'avatar_two',
                'type' => 'avatar',
                'label' => 'Avatar two',
                'path' => 'marketing/avatars/two.svg',
                'cdn_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=200&q=80',
                'meta' => [
                    'alt' => 'Traveler avatar illustration',
                    'source' => 'Unsplash / Aiony Haust',
                ],
            ],
            [
                'key' => 'avatar_three',
                'type' => 'avatar',
                'label' => 'Avatar three',
                'path' => 'marketing/avatars/three.svg',
                'cdn_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=200&q=80',
                'meta' => [
                    'alt' => 'Traveler avatar illustration',
                    'source' => 'Unsplash / Marvin Meyer',
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
