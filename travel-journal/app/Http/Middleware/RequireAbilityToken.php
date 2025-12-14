<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAbilityToken
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->currentAccessToken()) {
            abort(403, 'API token required');
        }

        return $next($request);
    }
}
