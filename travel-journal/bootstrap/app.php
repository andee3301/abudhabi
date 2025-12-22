<?php

use App\Http\Middleware\RequireAbilityToken;
use App\Http\Middleware\SetCacheHeaders as CustomCacheHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'cache.headers' => SetCacheHeaders::class,
            'cache.custom' => CustomCacheHeaders::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'ability.token' => RequireAbilityToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e): void {
            if (app()->environment('production')) {
                $webhook = config('logging.channels.slack.url');

                if ($webhook) {
                    Log::channel('slack')->critical($e->getMessage(), [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]);
                }
            }
        });
    })->create();
