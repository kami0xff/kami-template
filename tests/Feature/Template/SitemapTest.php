<?php

use App\Services\SeoManager;
use App\Services\Sitemap;
use Illuminate\Support\Facades\Route;

/*
 | Main-app sitemap
 */

it('writes the sitemap from registered sources', function () {
    app(Sitemap::class)->add('test', fn() => [
        Sitemap::entry('http://localhost/test-page', '2026-08-01'),
    ]);

    $output = sys_get_temp_dir() . '/kami-sitemap-test.xml';
    @unlink($output);

    $this->artisan('seo:sitemap', ['--output' => $output])->assertSuccessful();

    $xml = file_get_contents($output);
    @unlink($output);

    expect($xml)->toContain('xmlns:xhtml="http://www.w3.org/1999/xhtml"')
        // The 'pages' source from AppServiceProvider is always present:
        ->toContain('<loc>http://localhost</loc>')
        ->toContain('<loc>http://localhost/test-page</loc>')
        ->toContain('<lastmod>2026-08-01</lastmod>');
});

/**
 * Routes registered inside a test miss the boot-time name lookup refresh
 * (withRouting does it once on booted), so Route::has()/route() can't see
 * them until refreshed.
 */
function refreshRouteNameLookups(): void
{
    app('router')->getRoutes()->refreshNameLookups();
}

it('adds hreflang alternates to sitemap entries for localized routes', function () {
    config()->set('locales.supported', ['en' => [], 'es' => []]);

    Route::get('/colors', fn() => view('pages.home'))->name('colors');
    Route::get('/{locale}/colors', fn() => view('pages.home'))
        ->where(['locale' => 'es'])
        ->name('localized.colors');
    refreshRouteNameLookups();

    expect(Sitemap::forRoute('colors'))->toBe([
        'loc' => 'http://localhost/colors',
        'lastmod' => null,
        'alternates' => [
            'en' => 'http://localhost/colors',
            'es' => 'http://localhost/es/colors',
            'x-default' => 'http://localhost/colors',
        ],
    ]);
});

/*
 | hreflang head tags
 */

it('derives hreflang alternates from the current named route', function () {
    config()->set('locales.supported', ['en' => [], 'es' => []]);

    Route::get('/colors', fn() => view('pages.home'))->name('colors');
    Route::get('/{locale}/colors', fn() => view('pages.home'))
        ->where(['locale' => 'es'])
        ->name('localized.colors');
    refreshRouteNameLookups();

    $this->get('/colors')
        ->assertOk()
        ->assertSee('<link rel="alternate" hreflang="en" href="http://localhost/colors">', false)
        ->assertSee('<link rel="alternate" hreflang="es" href="http://localhost/es/colors">', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="http://localhost/colors">', false);

    // The localized URL renders the same self-referencing set.
    $this->get('/es/colors')
        ->assertOk()
        ->assertSee('<link rel="alternate" hreflang="en" href="http://localhost/colors">', false)
        ->assertSee('<link rel="alternate" hreflang="es" href="http://localhost/es/colors">', false);
});

it('lets explicit hreflang maps win over route-derived ones', function () {
    config()->set('locales.supported', ['en' => [], 'es' => []]);

    Route::get('/colors', function () {
        // Content pages with per-locale slugs (articles) set the map from
        // their translation data instead of relying on route params.
        seo()->hreflang([
            'en' => 'http://localhost/blog/hello',
            'es' => 'http://localhost/es/blog/hola',
            'x-default' => 'http://localhost/blog/hello',
        ]);

        return view('pages.home');
    })->name('colors');
    Route::get('/{locale}/colors', fn() => view('pages.home'))
        ->where(['locale' => 'es'])
        ->name('localized.colors');
    refreshRouteNameLookups();

    $this->get('/colors')
        ->assertOk()
        ->assertSee('hreflang="es" href="http://localhost/es/blog/hola"', false)
        ->assertDontSee('href="http://localhost/es/colors"', false);
});

it('emits no hreflang tags in single-locale apps', function () {
    // The template ships with only 'en' in config/locales.php, so the
    // localized route group is never registered and there is nothing to
    // alternate between.
    $this->get('/')->assertOk()->assertDontSee('hreflang', false);
});

/*
 | robots / noindex
 */

it('sets noindex robots via the shortcut', function () {
    $seo = app(SeoManager::class);

    $seo->noindex();
    expect($seo->get('robots'))->toBe('noindex, follow');

    $seo->noindex(follow: false);
    expect($seo->get('robots'))->toBe('noindex, nofollow');
});

/*
 | Static-site sitemap alternates
 */

it('cross-links locales in the site sitemap via xhtml:link', function () {
    $response = $this->get('http://example.test/sitemap.xml')->assertOk();

    // The home exists in en + es: each row links the other locale + x-default.
    $response->assertSee('<xhtml:link rel="alternate" hreflang="en" href="https://example.test/" />', false)
        ->assertSee('<xhtml:link rel="alternate" hreflang="es" href="https://example.test/es/" />', false)
        ->assertSee('<xhtml:link rel="alternate" hreflang="x-default" href="https://example.test/" />', false);
});
