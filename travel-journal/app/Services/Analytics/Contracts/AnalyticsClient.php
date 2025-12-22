<?php

namespace App\Services\Analytics\Contracts;

use Google\Analytics\Data\V1beta\RunReportResponse;

interface AnalyticsClient
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function runReport(array $payload): RunReportResponse;
}
