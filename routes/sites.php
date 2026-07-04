<?php

use App\Http\Controllers\Sites\BlogController;
use App\Http\Controllers\Sites\FeedController;
use App\Http\Controllers\Sites\ImageController;
use App\Http\Controllers\Sites\LeadController;
use App\Http\Controllers\Sites\PageController;
use App\Http\Controllers\Sites\SitemapController;
use App\Services\Sites\SiteRegistry;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Static Site Routes
|--------------------------------------------------------------------------
|
| One route group per registered domain of every site discovered under
| resources/sites/. This file is required at the TOP of routes/web.php so
| domain-scoped routes win over the main app's host-agnostic routes.
|
| URL structure per site:
|   /                 home  (site::pages.home Blade view or content/pages/home.md)
|   /blog             blog index (markdown posts, newest first)
|   /blog/{slug}      blog post   (content/blog/{slug}.md)
|   /feed.xml         RSS feed of blog posts
|   /lead   (POST)    lead capture — relayed to the admin project's API
|   /sitemap.xml      generated sitemap (all locales)
|   /{path}           static page (site::pages.{path} view or content/pages/{path}.md)
|
| Multi-locale sites ('locales' in site.php) additionally serve every
| localizable route under /{locale}/... for each extra locale — the default
| locale always stays at the root. Locale groups are registered before the
| root group so /es/... never falls into the root {path} catch-all.
|
| Opt-in: nothing is registered unless SITES_ENABLED=true (config/sites.php).
|
*/

if (!config('sites.enabled')) {
    return;
}

// Routes that exist once per locale (localized content).
$localized = function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('blog.show');
    Route::get('/feed.xml', [FeedController::class, 'index'])->name('feed');
    Route::get('/{path}', [PageController::class, 'show'])
        ->where('path', '[a-z0-9\-_\/]+')
        ->name('page');
};

foreach (app(SiteRegistry::class)->all() as $key => $site) {
    foreach ($site->domains as $i => $domain) {
        // The 'web' group is applied automatically (loaded via routes/web.php).
        Route::domain($domain)
            ->name("site.{$key}." . ($i === 0 ? '' : "alt{$i}."))
            ->group(function () use ($site, $key, $localized) {
                // Extra locales first: /{locale}/... with the locale baked
                // into the middleware parameters (no runtime detection).
                foreach ($site->extraLocales() as $locale) {
                    Route::prefix($locale)
                        ->middleware("site:{$key},{$locale}")
                        ->name("{$locale}.")
                        ->group($localized);
                }

                Route::middleware("site:{$key}")->group(function () use ($localized) {
                    // Locale-independent endpoints (single instance at the root).
                    Route::get('/search', [PageController::class, 'search'])->name('search');
                    // Content images + WebP variants (statically served once
                    // built; this route generates on demand for dev / misses).
                    Route::get('/images/{path}', [ImageController::class, 'show'])
                        ->where('path', '[A-Za-z0-9_\-\/\.]+')
                        ->name('image');
                    // Lead capture: CSRF-exempt (static pages carry no live
                    // token — see bootstrap/app.php), honeypot + throttled.
                    Route::post('/lead', [LeadController::class, 'store'])
                        ->middleware('throttle:leads')
                        ->name('lead');
                    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
                    Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

                    // Default locale at the root ({path} catch-all last).
                    $localized();
                });
            });
    }
}
