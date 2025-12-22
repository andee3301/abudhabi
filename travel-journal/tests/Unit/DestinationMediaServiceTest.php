<?php

namespace Tests\Unit;

use App\Models\MarketingAsset;
use App\Services\DestinationMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DestinationMediaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_wikipedia_summary_and_optimizes_wikimedia_image(): void
    {
        Cache::flush();
        Http::fake([
            'https://en.wikipedia.org/api/rest_v1/page/summary/*' => Http::response([
                'title' => 'Kyoto',
                'extract' => str_repeat('Kyoto is great. ', 30),
                'originalimage' => ['source' => 'https://upload.wikimedia.org/wikipedia/commons/a/a1/Kyoto.jpg'],
            ], 200),
        ]);

        $service = app(DestinationMediaService::class);

        $result = $service->for('Kyoto', 'jp');

        $this->assertSame('Kyoto', $result['title']);
        $this->assertNotNull($result['description']);
        $this->assertStringContainsString('upload.wikimedia.org', $result['image']);
        $this->assertStringContainsString('width=1200', $result['image']);
        $this->assertSame('wikimedia', $result['source']);

        // Second call should be cached (no extra HTTP requests)
        $service->for('Kyoto', 'jp');
        Http::assertSentCount(1);
    }

    public function test_it_falls_back_to_marketing_asset_when_unsplash_not_configured(): void
    {
        Cache::flush();

        MarketingAsset::create([
            'key' => 'trip_cover_default',
            'type' => 'image',
            'label' => 'Default trip cover',
            'path' => 'images/default.jpg',
            'cdn_url' => 'https://cdn.example.test/default.jpg',
            'meta' => [],
        ]);

        config([
            'services.unsplash.access_key' => null,
        ]);

        Http::fake([
            'https://en.wikipedia.org/api/rest_v1/page/summary/*' => Http::response([], 500),
            'https://api.unsplash.com/search/photos*' => Http::response([], 500),
        ]);

        $service = app(DestinationMediaService::class);

        $result = $service->for('Lisbon', 'pt');

        $this->assertSame('https://cdn.example.test/default.jpg', $result['image']);
        $this->assertSame('unsplash', $result['source']);
        Http::assertSentCount(1);
    }
}
