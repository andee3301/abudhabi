<?php

namespace Tests\Unit;

use App\Services\Analytics\Contracts\AnalyticsClient;
use App\Services\Analytics\GoogleAnalyticsService;
use Google\Analytics\Data\V1beta\RunReportResponse;
use PHPUnit\Framework\TestCase;

class GoogleAnalyticsServiceTest extends TestCase
{
    public function test_it_runs_report_with_configured_property(): void
    {
        $response = $this->createMock(RunReportResponse::class);

        $client = $this->createMock(AnalyticsClient::class);
        $client->expects($this->once())
            ->method('runReport')
            ->with($this->callback(function (array $payload): bool {
                return $payload['property'] === 'properties/test-prop'
                    && count($payload['dimensions']) === 1
                    && count($payload['metrics']) === 1;
            }))
            ->willReturn($response);

        $service = new GoogleAnalyticsService($client, 'test-prop');

        $result = $service->runReport(['city'], ['activeUsers']);

        $this->assertSame($response, $result);
    }
}
