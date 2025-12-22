<?php

namespace App\Services\Analytics\Clients;

use App\Services\Analytics\Contracts\AnalyticsClient;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportResponse;

class GoogleAnalyticsClient implements AnalyticsClient
{
    public function __construct(private readonly BetaAnalyticsDataClient $client) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function runReport(array $payload): RunReportResponse
    {
        return $this->client->runReport($payload);
    }
}
