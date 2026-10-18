<?php

namespace App\Http\Middleware;

use App\Services\Kami\KamiApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives every browser a random, http-only visitor id (config kami.visitor_cookie),
 * forwarded by KamiApi::visitor() as X-Visitor-Id so the admin counts a
 * logged-out visitor's views once. Inactive until the network client is
 * configured, so static-only projects never set the cookie.
 */
class EnsureVisitorId
{
    public function handle(Request $request, Closure $next): Response
    {
        $name = (string) config('kami.visitor_cookie');

        if (!KamiApi::configured() || $request->cookies->has($name)) {
            return $next($request);
        }

        $id = (string) Str::uuid();
        // Visible to this request's API calls before the browser sends it back.
        $request->cookies->set($name, $id);
        Cookie::queue(Cookie::make($name, $id, 60 * 24 * 365 * 2, httpOnly: true, sameSite: 'lax'));

        return $next($request);
    }
}
