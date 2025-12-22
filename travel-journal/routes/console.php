<?php

use App\Services\SlackNotifier;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('slack:notify {message : Message to send} {--context=* : Optional key=value pairs for attachment fields}', function (SlackNotifier $notifier) {
    $context = [];

    foreach ($this->option('context') as $pair) {
        if (! str_contains($pair, '=')) {
            $this->warn("Ignoring context value [{$pair}] because it is not key=value.");

            continue;
        }

        [$key, $value] = explode('=', $pair, 2);
        $context[$key] = $value;
    }

    $sent = $notifier->notify($this->argument('message'), $context);

    if ($sent) {
        $this->info('Slack notification sent.');

        return;
    }

    $this->error('Slack notification failed. Check logs for details.');
})->purpose('Send a test message to the configured Slack webhook');
