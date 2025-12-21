<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotifier
{
    public function __construct(private readonly ?string $webhookUrl = null)
    {
    }

    public function notify(string $message, array $context = []): bool
    {
        $webhook = $this->webhookUrl ?: config('services.slack.webhook_url');

        if (empty($webhook)) {
            Log::warning('Slack webhook URL not configured; skipping notification.', [
                'message' => $message,
            ]);

            return false;
        }

        $payload = ['text' => $message];

        if (! empty($context)) {
            $fields = [];

            foreach ($context as $key => $value) {
                $fields[] = [
                    'title' => (string) $key,
                    'value' => is_scalar($value) ? (string) $value : json_encode($value),
                    'short' => true,
                ];
            }

            $payload['attachments'] = [
                [
                    'color' => '#0b74de',
                    'fields' => $fields,
                ],
            ];
        }

        $response = Http::post($webhook, $payload);

        if ($response->failed()) {
            Log::error('Failed to post Slack notification.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
