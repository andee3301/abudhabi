<?php

namespace Tests\Unit;

use App\Services\TimeZoneHelper;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class TimeZoneHelperTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_diff_in_hours_returns_null_for_missing_or_invalid_timezones(): void
    {
        $helper = new TimeZoneHelper();

        $this->assertNull($helper->diffInHours(null, 'UTC'));
        $this->assertNull($helper->diffInHours('UTC', null));
        $this->assertNull($helper->diffInHours('Not/A_Real_TZ', 'UTC'));
        $this->assertNull($helper->diffInHours('UTC', 'Not/A_Real_TZ'));
    }

    public function test_diff_in_hours_computes_offset_difference(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 15, 12, 0, 0, 'UTC'));

        $helper = new TimeZoneHelper();

        $this->assertSame(4, $helper->diffInHours('UTC', 'Asia/Dubai'));
        $this->assertSame(-4, $helper->diffInHours('Asia/Dubai', 'UTC'));
    }

    public function test_now_in_returns_now_in_timezone_or_falls_back(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 15, 12, 0, 0, 'UTC'));

        $helper = new TimeZoneHelper();

        $this->assertSame('UTC', $helper->nowIn(null)->getTimezone()->getName());
        $this->assertSame('Asia/Dubai', $helper->nowIn('Asia/Dubai')->getTimezone()->getName());
        $this->assertSame('UTC', $helper->nowIn('Not/A_Real_TZ')->getTimezone()->getName());
    }
}
