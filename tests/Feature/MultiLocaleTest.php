<?php

use Illuminate\Support\Facades\File;

it('serves the extra locale home under its prefix with localized data', function () {
    // The example home is a Blade view: it serves every locale, with the
    // posts collection localized (only translated posts appear).
    $this->get('http://example.test/es/')
        ->assertOk()
        ->assertSee('lang="es"', false)
        ->assertSee('Últimos artículos')
        ->assertSee('Hola Mundo')
        ->assertDontSee('Writing Content in Markdown');
});

it('serves a translated blog post with hreflang alternates', function () {
    $this->get('http://example.test/es/blog/hello-world')
        ->assertOk()
        ->assertSee('lang="es"', false)
        ->assertSee('Hola Mundo')
        // Canonical points at the localized URL.
        ->assertSee('<link rel="canonical" href="https://example.test/es/blog/hello-world">', false)
        // hreflang pair + x-default (default locale).
        ->assertSee('hreflang="en" href="https://example.test/blog/hello-world"', false)
        ->assertSee('hreflang="es" href="https://example.test/es/blog/hello-world"', false)
        ->assertSee('hreflang="x-default" href="https://example.test/blog/hello-world"', false);
});

it('adds hreflang and a language switcher to the default-locale version', function () {
    $this->get('http://example.test/blog/hello-world')
        ->assertOk()
        ->assertSee('hreflang="es" href="https://example.test/es/blog/hello-world"', false)
        // Switcher links to the translation, labeled with the locale code.
        ->assertSee('rel="alternate">ES</a>', false);
});

it('emits no hreflang for untranslated documents', function () {
    $this->get('http://example.test/blog/writing-content')
        ->assertOk()
        ->assertDontSee('hreflang="es"', false);
});

it('serves a per-locale rss feed', function () {
    $this->get('http://example.test/es/feed.xml')
        ->assertOk()
        ->assertSee('https://example.test/es/blog/hello-world', false)
        ->assertDontSee('https://example.test/es/blog/writing-content', false);
});

it('includes every locale in the sitemap', function () {
    $this->get('http://example.test/sitemap.xml')
        ->assertOk()
        ->assertSee('<loc>https://example.test/blog/hello-world</loc>', false)
        ->assertSee('<loc>https://example.test/es/</loc>', false)
        ->assertSee('<loc>https://example.test/es/blog/hello-world</loc>', false)
        // Untranslated post exists only in the default locale.
        ->assertDontSee('<loc>https://example.test/es/blog/writing-content</loc>', false);
});

it('404s on locales the site does not declare', function () {
    $this->get('http://example.test/de/blog/hello-world')->assertNotFound();
});

it('builds the static output for every locale', function () {
    $this->artisan('site:build', ['site' => 'example'])->assertSuccessful();

    expect(public_path('static/example.test/es/index.html'))->toBeFile()
        ->and(public_path('static/example.test/es/blog/hello-world/index.html'))->toBeFile()
        ->and(public_path('static/example.test/es/feed.xml'))->toBeFile()
        ->and(file_get_contents(public_path('static/example.test/es/index.html')))->toContain('lang="es"');

    File::deleteDirectory(public_path('static/example.test'));
});
