<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class TripCache
{
    /**
     * Get cache key for user's trip list
     */
    public static function listKey(int $userId, ?string $status = null, ?string $search = null): string
    {
        $parts = ["trips:user:{$userId}:list"];
        if ($status) {
            $parts[] = "status:{$status}";
        }
        if ($search) {
            $parts[] = 'search:'.md5($search);
        }

        return implode(':', $parts);
    }

    /**
     * Get cache key for single trip
     */
    public static function showKey(int $tripId): string
    {
        return "trips:trip:{$tripId}:show";
    }

    /**
     * Remember trip list with cache tags
     */
    public static function rememberList(int $userId, ?string $status, ?string $search, callable $callback, int $seconds = 600)
    {
        return Cache::tags(['trips', "user:{$userId}"])
            ->remember(self::listKey($userId, $status, $search), $seconds, $callback);
    }

    /**
     * Remember single trip with cache tags
     */
    public static function rememberShow(int $tripId, int $userId, callable $callback, int $seconds = 600)
    {
        return Cache::tags(['trips', "user:{$userId}", "trip:{$tripId}"])
            ->remember(self::showKey($tripId), $seconds, $callback);
    }

    /**
     * Flush all trip caches for a user
     */
    public static function flushUser(int $userId): void
    {
        Cache::tags(["user:{$userId}"])->flush();
    }

    /**
     * Flush cache for a specific trip
     */
    public static function flushTrip(int $tripId, int $userId): void
    {
        Cache::tags(["trip:{$tripId}"])->flush();
        Cache::tags(["user:{$userId}"])->flush();
    }
}
