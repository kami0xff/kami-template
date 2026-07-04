<?php

use App\Http\Controllers\Sites\BlogController;
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
|   /sitemap.xml      generated sitemap
|   /{path}           static page (site::pages.{path} view or content/pages/{path}.md)
|
*/

foreach (app(SiteRegistry::class)->all() as $key => $site) {
    foreach ($site->domains as $i => $domain) {
        // The 'web' group is applied automatically (loaded via routes/web.php).
        Route::domain($domain)
            ->middleware("site:{$key}")
            ->name("site.{$key}." . ($i === 0 ? '' : "alt{$i}."))
            ->group(function () {
                Route::get('/', [PageController::class, 'home'])->name('home');
                Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
                Route::get('/blog/{slug}', [BlogController::class, 'show'])
                    ->where('slug', '[a-z0-9\-]+')
                    ->name('blog.show');
                Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
                Route::get('/{path}', [PageController::class, 'show'])
                    ->where('path', '[a-z0-9\-_\/]+')
                    ->name('page');
            });
    }
}
