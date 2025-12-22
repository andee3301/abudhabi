<?php

namespace Tests\Unit;

use App\Http\Middleware\SetCacheHeaders;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SetCacheHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sets_cache_headers_and_etag_for_guest_get_requests(): void
    {
        $middleware = new SetCacheHeaders();

        $request = Request::create('/public', 'GET');

        $response = $middleware->handle($request, fn () => response('hello'), '600');

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=600', $cacheControl);

        $etag = md5('hello');
        $this->assertSame($etag, $response->headers->get('ETag'));

        $requestWithEtag = Request::create('/public', 'GET', server: ['HTTP_IF_NONE_MATCH' => $etag]);
        $notModified = $middleware->handle($requestWithEtag, fn () => response('hello'), '600');

        $this->assertSame(304, $notModified->getStatusCode());
    }

    public function test_it_does_not_set_cache_headers_for_authenticated_or_non_get_requests(): void
    {
        $middleware = new SetCacheHeaders();
        $user = User::factory()->create();

        $authedRequest = Request::create('/public', 'GET');
        $authedRequest->setUserResolver(fn () => $user);

        $authedResponse = $middleware->handle($authedRequest, fn () => response('hello'), '600');
        $this->assertStringNotContainsString('max-age=600', (string) $authedResponse->headers->get('Cache-Control'));
        $this->assertStringNotContainsString('public', (string) $authedResponse->headers->get('Cache-Control'));

        $postRequest = Request::create('/public', 'POST');
        $postResponse = $middleware->handle($postRequest, fn () => response('hello'), '600');
        $this->assertStringNotContainsString('max-age=600', (string) $postResponse->headers->get('Cache-Control'));
        $this->assertStringNotContainsString('public', (string) $postResponse->headers->get('Cache-Control'));
    }
}
