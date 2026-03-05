<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Detects browser Accept-Language and redirects to locale-prefixed URL on first visit.
 * A cookie remembers the detection so it only fires once.
 */
class DetectLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->expectsJson() ||
            $request->ajax() ||
            $request->is('api/*') ||
            $request->is('sitemap*') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        if ($request->cookie('locale_detected')) {
            return $next($request);
        }

        $preferredLocale = $this->detectBrowserLocale($request);

        if ($preferredLocale && $preferredLocale !== 'en') {
            $path = $request->path();
            $query = $request->getQueryString();
            $redirectUrl = url('/' . $preferredLocale . '/' . ($path === '/' ? '' : $path));
            if ($query) {
                $redirectUrl .= '?' . $query;
            }

            return redirect($redirectUrl)
                ->cookie('locale_detected', $preferredLocale, 60 * 24 * 365);
        }

        $response = $next($request);
        return $response->cookie('locale_detected', 'en', 60 * 24 * 365);
    }

    protected function detectBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (empty($acceptLanguage)) {
            return null;
        }

        $supportedLocales = array_keys(config('locales.supported', []));

        $preferred = [];
        foreach (explode(',', $acceptLanguage) as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            $segments = explode(';', $part);
            $locale = trim($segments[0]);
            $quality = 1.0;

            if (isset($segments[1])) {
                $qPart = trim($segments[1]);
                if (str_starts_with($qPart, 'q=')) {
                    $quality = (float) substr($qPart, 2);
                }
            }

            $preferred[$locale] = $quality;
        }

        arsort($preferred);

        foreach ($preferred as $browserLocale => $quality) {
            $normalized = str_replace('_', '-', $browserLocale);

            if (in_array($normalized, $supportedLocales)) {
                return $normalized;
            }

            $base = explode('-', $normalized)[0];
            if (in_array($base, $supportedLocales)) {
                return $base;
            }
        }

        return null;
    }
}
