<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeZone;

class TimeZoneHelper
{
    public function diffInHours(?string $fromTz, ?string $toTz): ?int
    {
        if (! $fromTz || ! $toTz) {
            return null;
        }

        try {
            $from = new DateTimeZone($fromTz);
            $to = new DateTimeZone($toTz);
        } catch (\Exception) {
            return null;
        }

        $now = Carbon::now();

        return ($now->clone()->setTimezone($to)->offset - $now->clone()->setTimezone($from)->offset) / 3600;
    }

    public function nowIn(?string $timezone): Carbon
    {
        if (! $timezone) {
            return Carbon::now();
        }

        try {
            return Carbon::now(new DateTimeZone($timezone));
        } catch (\Exception) {
            return Carbon::now();
        }
    }
}
