<?php

namespace App\Services;

use App\Support\MarketingAssetRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DestinationMediaService
{
    protected const CACHE_TTL = 3600; // seconds

    public function __construct(private MarketingAssetRepository $assets) {}

    public function for(string $city, ?string $countryCode = null): array
    {
        $key = sprintf('destination_media:%s:%s', Str::slug($city), strtolower($countryCode ?? 'any'));

        return Cache::remember($key, self::CACHE_TTL, function () use ($city, $countryCode) {
            $summary = $this->fetchWikipediaSummary($city, $countryCode);
            $image = $summary['image'] ?? null;

            if (! $image) {
                $image = $this->unsplashFallback($city, $countryCode);
            }

            return [
                'title' => $summary['title'] ?? $city,
                'description' => $summary['description'] ?? null,
                'image' => $image,
                'source' => $summary['source'] ?? ($image ? 'unsplash' : 'fallback'),
            ];
        });
    }

    protected function fetchWikipediaSummary(string $city, ?string $countryCode = null): array
    {
        $title = $countryCode ? sprintf('%s, %s', $city, strtoupper($countryCode)) : $city;
        $encoded = rawurlencode($title);
        $endpoint = "https://en.wikipedia.org/api/rest_v1/page/summary/{$encoded}";

        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->withOptions(['http_errors' => false])
                ->withUserAgent(config('app.name', 'Travel Journal').' bot/1.0')
                ->get($endpoint);
        } catch (\Throwable $e) {
            Log::warning('destination_media.wikipedia_failed', [
                'city' => $city,
                'country' => $countryCode,
                'error' => $e->getMessage(),
            ]);

            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $json = $response->json();
        $image = Arr::get($json, 'originalimage.source') ?? Arr::get($json, 'thumbnail.source');
        $description = Arr::get($json, 'extract');

        if ($image && str_contains($image, 'upload.wikimedia.org')) {
            $image = $this->optimizeWikimediaUrl($image);
        }

        return [
            'title' => Arr::get($json, 'title', $city),
            'description' => $description ? Str::of($description)->words(80)->toString() : null,
            'image' => $image,
            'source' => 'wikimedia',
        ];
    }

    protected function unsplashFallback(string $city, ?string $countryCode): ?string
    {
        $accessKey = config('services.unsplash.access_key');

        if (! $accessKey) {
            return $this->assets->url('trip_cover_default');
        }

        $query = $countryCode ? "$city $countryCode travel" : "$city travel";
        try {
            $response = Http::acceptJson()
                ->withToken($accessKey)
                ->timeout(5)
                ->withOptions(['http_errors' => false])
                ->withUserAgent(config('app.name', 'Travel Journal').' bot/1.0')
                ->get('https://api.unsplash.com/search/photos', [
                    'query' => $query,
                    'per_page' => 1,
                    'orientation' => 'landscape',
                ]);
        } catch (\Throwable $e) {
            Log::warning('destination_media.unsplash_failed', [
                'city' => $city,
                'country' => $countryCode,
                'error' => $e->getMessage(),
            ]);

            return $this->assets->url('trip_cover_default');
        }

        if (! $response->successful()) {
            return $this->assets->url('trip_cover_default');
        }

        return Arr::get($response->json(), 'results.0.urls.regular', $this->assets->url('trip_cover_default'));
    }

    protected function optimizeWikimediaUrl(string $url): string
    {
        // Wikimedia originals are large; prefer width param when available
        return Str::contains($url, '?') ? $url.'&width=1200' : $url.'?width=1200';
    }
}
