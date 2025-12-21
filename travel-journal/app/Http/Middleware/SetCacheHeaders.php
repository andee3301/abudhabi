<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $maxAge = '3600'): Response
    {
        $response = $next($request);

        // Don't cache if not a GET request or if user is authenticated
        if (! $request->isMethod('GET') || $request->user()) {
            return $response;
        }

        // Set cache headers
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}");

        // Add ETag for conditional requests
        if ($response->getContent()) {
            $etag = md5($response->getContent());
            $response->headers->set('ETag', $etag);

            // Check if client has cached version
            if ($request->getETags() && in_array($etag, $request->getETags())) {
                $response->setNotModified();
            }
        }

        return $response;
    }
}
