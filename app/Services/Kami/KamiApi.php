<?php

namespace App\Services\Kami;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Server-to-server client for the Kami admin APIs (config/kami.php).
 *
 * Every non-2xx answer throws a KamiApiException carrying the admin's error
 * code, so callers branch on `$e->code` and Laravel renders the common cases
 * (expired login, validation, maintenance) without per-controller handling.
 *
 * Stateless on purpose: under Octane the instance outlives a request, so the
 * visitor's session and cookie are read at call time, never stored here.
 *
 *   $api->visitor()->get('/feed', ['page' => 2])->json('data');
 *   $api->network()->post('/auth/login', [...])->json('data');
 */
final class KamiApi
{
    public function __construct(private readonly VisitorSession $session) {}

    public static function configured(): bool
    {
        return filled(config('kami.admin_url')) && filled(config('kami.site_token'));
    }

    /** Network API as the site only (public reads, login, register). */
    public function network(): PendingRequest
    {
        if (!self::configured()) {
            throw new RuntimeException('KAMI_ADMIN_URL and KAMI_SITE_TOKEN must be set (config/kami.php).');
        }

        return $this->request('/api/network/v1')
            ->withHeaders(['X-Site-Token' => (string) config('kami.site_token')]);
    }

    /**
     * Network API on behalf of the current visitor: their account token when
     * logged in, and their browser's visitor id for anonymous view counts.
     */
    public function visitor(): PendingRequest
    {
        $request = $this->network();

        if ($token = $this->session->token()) {
            $request->withToken($token);
        }
        if ($visitorId = request()->cookie((string) config('kami.visitor_cookie'))) {
            $request->withHeaders(['X-Visitor-Id' => (string) $visitorId]);
        }

        return $request;
    }

    /** Network API with an explicit account token (e.g. right after login). */
    public function asVisitor(string $accountToken): PendingRequest
    {
        return $this->network()->withToken($accountToken);
    }

    /** Public catalog API: no auth. */
    public function catalog(): PendingRequest
    {
        return $this->request('/api/v1');
    }

    /** Warehouse API: the project's warehouse Bearer token. */
    public function warehouse(): PendingRequest
    {
        if (blank(config('kami.warehouse_token'))) {
            throw new RuntimeException('KAMI_WAREHOUSE_TOKEN must be set (config/kami.php).');
        }

        return $this->request('/api/v1')->withToken((string) config('kami.warehouse_token'));
    }

    private function request(string $prefix): PendingRequest
    {
        if (blank(config('kami.admin_url'))) {
            throw new RuntimeException('KAMI_ADMIN_URL must be set (config/kami.php).');
        }

        return Http::baseUrl(rtrim((string) config('kami.admin_url'), '/') . $prefix)
            ->acceptJson()
            ->timeout((int) config('kami.timeout', 10))
            ->throw(function (Response $response, RequestException $e) {
                throw KamiApiException::fromResponse($response, $e);
            });
    }
}
