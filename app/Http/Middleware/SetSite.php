<?php

namespace App\Http\Middleware;

use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Scopes the request to one static site (parameterized: `site:{key}` or
 * `site:{key},{locale}` for a locale-prefixed route group).
 *
 * - 301-redirects secondary domains (e.g. www.) to the canonical domain.
 * - Overrides app.name/app.url and the seo config with the site's values.
 * - Sets the app locale (route-group locale, or the site's default).
 * - Registers the `site::` view namespace: the site's own views first,
 *   then resources/views/sites as shared fallbacks.
 * - Binds the current Site instance in the container and shares it with views.
 */

//TODO consider this approach since we are now using cloudflare for the hosting of multi static sites 
//maybe if one day its no longer possible with cloudflare it would be nice to have a home made alternative ready 
class SetSite
{
    public function __construct(protected SiteRegistry $registry) {}

    public function handle(Request $request, Closure $next, string $siteKey, ?string $locale = null): Response
    {
        $site = $this->registry->get($siteKey);

        abort_if($site === null, 404);
        abort_if($locale !== null && !in_array($locale, $site->locales, true), 404);

        if ($request->getHost() !== $site->canonicalDomain()) {
            return redirect()->away($site->url($request->getRequestUri()), 301);
        }

        config([
            'app.name' => $site->name,
            'app.url' => rtrim($site->url('/'), '/'),
            'seo' => $site->seoConfig(app('seo.defaults')),
        ]);

        app()->setLocale($locale ?? $site->locale);

        // Per-site UI strings (see Site::langPath). The translator and its
        // loader are rebuilt per request so one site's lang files can never
        // leak into another site's — or the main app's — translations in
        // long-running processes (Octane workers, site:build). The fresh
        // translator picks up the locale set above from config.
        app()->forgetInstance('translator');
        app()->forgetInstance('translation.loader');

        if (is_dir($langPath = $site->langPath())) {
            app('translation.loader')->addPath($langPath);
        }

        View::replaceNamespace('site', [
            $site->viewsPath(),
            resource_path('views/sites'),
        ]);
        View::share('site', $site);

        app()->instance(Site::class, $site);

        return $next($request);
    }
}
