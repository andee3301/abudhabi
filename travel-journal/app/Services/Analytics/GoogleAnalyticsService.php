<?php

namespace App\Services\Analytics;

use App\Services\Analytics\Contracts\AnalyticsClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportResponse;
use InvalidArgumentException;

class GoogleAnalyticsService
{
    public function __construct(
        private readonly AnalyticsClient $client,
        private readonly ?string $propertyId,
    ) {}

    public function runReport(
        array $dimensions,
        array $metrics,
        array $dateRange = ['start_date' => '7daysAgo', 'end_date' => 'today']
    ): RunReportResponse {
        if (! $this->propertyId) {
            throw new InvalidArgumentException('GA4 property ID is not configured. Set GA4_PROPERTY_ID.');
        }

        $dimensionObjects = array_map(function (string $name): Dimension {
            $dimension = new Dimension;
            $dimension->setName($name);

            return $dimension;
        }, $dimensions);

        $metricObjects = array_map(function (string $name): Metric {
            $metric = new Metric;
            $metric->setName($name);

            return $metric;
        }, $metrics);

        $range = new DateRange;
        $range->setStartDate($dateRange['start_date'] ?? '7daysAgo');
        $range->setEndDate($dateRange['end_date'] ?? 'today');

        return $this->client->runReport([
            'property' => sprintf('properties/%s', $this->propertyId),
            'dateRanges' => [$range],
            'dimensions' => $dimensionObjects,
            'metrics' => $metricObjects,
        ]);
    }
}
