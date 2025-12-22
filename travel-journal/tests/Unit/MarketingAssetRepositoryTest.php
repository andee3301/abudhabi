<?php

namespace Tests\Unit;

use App\Models\MarketingAsset;
use App\Support\MarketingAssetRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MarketingAssetRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_url_returns_placeholder_when_asset_missing(): void
    {
        Cache::flush();

        $repo = app(MarketingAssetRepository::class);

        $url = $repo->url('missing-key');

        $this->assertStringContainsString('/images/placeholder.svg', $url);
    }

    public function test_url_prefers_cdn_url_when_present(): void
    {
        Cache::flush();

        MarketingAsset::create([
            'key' => 'hero',
            'type' => 'image',
            'label' => 'Hero',
            'path' => 'images/hero.jpg',
            'cdn_url' => 'https://cdn.example.test/hero.jpg',
            'meta' => ['alt' => 'Hero image'],
        ]);

        $repo = app(MarketingAssetRepository::class);

        $this->assertSame('https://cdn.example.test/hero.jpg', $repo->url('hero'));
    }

    public function test_url_uses_configured_cdn_base_when_available(): void
    {
        Cache::flush();

        config(['services.marketing_assets.cdn_base_url' => 'https://cdn.example.test/base']);

        MarketingAsset::create([
            'key' => 'trip_cover_default',
            'type' => 'image',
            'label' => 'Default trip cover',
            'path' => 'images/default.jpg',
            'cdn_url' => null,
            'meta' => [],
        ]);

        $repo = app(MarketingAssetRepository::class);

        $this->assertSame('https://cdn.example.test/base/images/default.jpg', $repo->url('trip_cover_default'));
    }

    public function test_metadata_can_be_retrieved_with_dot_path(): void
    {
        Cache::flush();

        MarketingAsset::create([
            'key' => 'foo',
            'type' => 'image',
            'label' => 'Foo',
            'path' => 'images/foo.jpg',
            'cdn_url' => null,
            'meta' => ['a' => ['b' => 123]],
        ]);

        $repo = app(MarketingAssetRepository::class);

        $this->assertSame(123, $repo->metadata('foo', 'a.b'));
    }
}
