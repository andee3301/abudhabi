<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class DashboardCache
{
    public static function key(int $userId, string $segment): string
    {
        return "dashboard_{$userId}_{$segment}";
    }

    public static function remember(int $userId, string $segment, callable $callback, int $seconds = 300)
    {
        return Cache::remember(self::key($userId, $segment), $seconds, $callback);
    }

    public static function flush(int $userId): void
    {
        Cache::forget(self::key($userId, 'cards'));
        Cache::forget(self::key($userId, 'stats'));
        Cache::forget(self::key($userId, 'timeline'));
    }
}
