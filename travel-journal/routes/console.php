<?php

use App\Services\SlackNotifier;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

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

Artisan::command('app:ready {--skip-assets : Skip Vite production build} {--skip-migrate : Skip running database migrations}', function () {
    $this->info('Preparing application for production...');

    if (! $this->option('skip-migrate')) {
        $this->warn('Running database migrations...');
        $this->call('migrate', ['--force' => true]);
    } else {
        $this->warn('Skipping migrations.');
    }

    $this->warn('Linking storage...');
    $this->call('storage:link');

    foreach (['config:cache', 'route:cache', 'view:cache', 'event:cache'] as $command) {
        $this->warn("Running {$command}...");
        $this->call($command);
    }

    if ($this->option('skip-assets')) {
        $this->warn('Skipping frontend build.');

        return;
    }

    $this->warn('Building frontend assets for production...');

    $process = Process::fromShellCommandline('npm run build');
    $process->setTimeout(null);
    $process->run(function (string $type, string $buffer): void {
        $this->output->write($buffer);
    });

    if (! $process->isSuccessful()) {
        $this->error('Frontend build failed. Check the logs above for details.');

        return 1;
    }

    $this->info('Application is ready for production.');
})->purpose('Cache config/routes/views and build assets for production');
