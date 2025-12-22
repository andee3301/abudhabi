<?php

namespace App\Support;

use App\Models\MarketingAsset;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class MarketingAssetRepository
{
    public function url(string $key, ?string $fallback = null): string
    {
        $asset = $this->find($key);

        if (! $asset) {
            return $fallback ?? asset('images/placeholder.svg');
        }

        if ($asset->cdn_url) {
            return $asset->cdn_url;
        }

        $cdnBase = config('services.marketing_assets.cdn_base_url');

        if ($cdnBase) {
            return rtrim($cdnBase, '/').'/'.ltrim($asset->path, '/');
        }

        return asset($asset->path);
    }

    public function metadata(string $key, ?string $dotPath = null): mixed
    {
        $asset = $this->find($key);

        if (! $asset) {
            return null;
        }

        return $dotPath ? Arr::get($asset->meta, $dotPath) : $asset->meta;
    }

    private function find(string $key): ?MarketingAsset
    {
        return Cache::rememberForever("marketing_asset_{$key}", function () use ($key) {
            return MarketingAsset::query()->where('key', $key)->first();
        });
    }
}
