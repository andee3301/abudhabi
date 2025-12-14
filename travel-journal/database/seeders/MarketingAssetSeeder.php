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
                'meta' => [
                    'alt' => 'World map wireframe',
                    'source' => 'Local demo asset',
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
                'meta' => [
                    'country' => 'PT',
                    'source' => 'Local flag asset',
                ],
            ],
            [
                'key' => 'flag_jp',
                'type' => 'flag',
                'label' => 'Japan flag',
                'path' => 'marketing/flags/jp.svg',
                'meta' => [
                    'country' => 'JP',
                    'source' => 'Local flag asset',
                ],
            ],
            [
                'key' => 'flag_eg',
                'type' => 'flag',
                'label' => 'Egypt flag',
                'path' => 'marketing/flags/eg.svg',
                'meta' => [
                    'country' => 'EG',
                    'source' => 'Local flag asset',
                ],
            ],
            [
                'key' => 'flag_us',
                'type' => 'flag',
                'label' => 'United States flag',
                'path' => 'marketing/flags/us.svg',
                'meta' => [
                    'country' => 'US',
                    'source' => 'Local flag asset',
                ],
            ],
            [
                'key' => 'flag_mx',
                'type' => 'flag',
                'label' => 'Mexico flag',
                'path' => 'marketing/flags/mx.svg',
                'meta' => [
                    'country' => 'MX',
                    'source' => 'Local flag asset',
                ],
            ],
            [
                'key' => 'gallery_camera',
                'type' => 'gallery',
                'label' => 'Camera illustration',
                'path' => 'marketing/gallery/camera.svg',
                'meta' => [
                    'alt' => 'Illustration of a travel camera',
                    'source' => 'Local gallery asset',
                ],
            ],
            [
                'key' => 'gallery_journal',
                'type' => 'gallery',
                'label' => 'Journal illustration',
                'path' => 'marketing/gallery/journal.svg',
                'meta' => [
                    'alt' => 'Illustration of a traveler writing',
                    'source' => 'Local gallery asset',
                ],
            ],
            [
                'key' => 'gallery_mountain',
                'type' => 'gallery',
                'label' => 'Mountain illustration',
                'path' => 'marketing/gallery/mountain.svg',
                'meta' => [
                    'alt' => 'Illustration of a mountain sunset',
                    'source' => 'Local gallery asset',
                ],
            ],
            [
                'key' => 'avatar_one',
                'type' => 'avatar',
                'label' => 'Avatar one',
                'path' => 'marketing/avatars/one.svg',
                'meta' => [
                    'alt' => 'Traveler avatar illustration',
                    'source' => 'Local avatar asset',
                ],
            ],
            [
                'key' => 'avatar_two',
                'type' => 'avatar',
                'label' => 'Avatar two',
                'path' => 'marketing/avatars/two.svg',
                'meta' => [
                    'alt' => 'Traveler avatar illustration',
                    'source' => 'Local avatar asset',
                ],
            ],
            [
                'key' => 'avatar_three',
                'type' => 'avatar',
                'label' => 'Avatar three',
                'path' => 'marketing/avatars/three.svg',
                'meta' => [
                    'alt' => 'Traveler avatar illustration',
                    'source' => 'Local avatar asset',
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
