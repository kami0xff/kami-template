<?php

namespace App\Http\Middleware;

use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Scopes the request to one static site (parameterized: `site:{key}`).
 *
 * - 301-redirects secondary domains (e.g. www.) to the canonical domain.
 * - Overrides app.name/app.url and the seo config with the site's values.
 * - Registers the `site::` view namespace: the site's own views first,
 *   then resources/views/sites as shared fallbacks.
 * - Binds the current Site instance in the container and shares it with views.
 */
class SetSite
{
    public function __construct(protected SiteRegistry $registry)
    {
    }

    public function handle(Request $request, Closure $next, string $siteKey): Response
    {
        $site = $this->registry->get($siteKey);

        abort_if($site === null, 404);

        if ($request->getHost() !== $site->canonicalDomain()) {
            return redirect()->away($site->url($request->getRequestUri()), 301);
        }

        config([
            'app.name' => $site->name,
            'app.url' => rtrim($site->url('/'), '/'),
            'seo' => $site->seoConfig(app('seo.defaults')),
        ]);

        app()->setLocale($site->locale);

        View::replaceNamespace('site', [
            $site->viewsPath(),
            resource_path('views/sites'),
        ]);
        View::share('site', $site);

        app()->instance(Site::class, $site);

        return $next($request);
    }
}
