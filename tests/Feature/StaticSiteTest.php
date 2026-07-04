<?php

use Illuminate\Support\Facades\File;

it('serves a site home page by domain', function () {
    $this->get('http://example.test/')
        ->assertOk()
        ->assertSee('Example Site')
        ->assertSee('Hello World');
});

it('does not affect the main app routes', function () {
    $this->get('/up')->assertOk();
    $this->get('/')->assertOk();
});

it('redirects secondary domains to the canonical domain', function () {
    $this->get('http://www.example.test/about')
        ->assertRedirect('https://example.test/about');
});

it('serves markdown static pages with front matter SEO', function () {
    $this->get('http://example.test/about')
        ->assertOk()
        ->assertSee('<title>About | Example Site</title>', false)
        ->assertSee('About this site')
        ->assertSee('rel="canonical" href="https://example.test/about"', false);
});

it('serves the blog index with published posts only', function () {
    $this->get('http://example.test/blog')
        ->assertOk()
        ->assertSee('Hello World')
        ->assertSee('Writing Content in Markdown')
        ->assertDontSee('Unpublished Draft');
});

it('serves a blog post with article SEO and schema', function () {
    $this->get('http://example.test/blog/hello-world')
        ->assertOk()
        ->assertSee('<title>Hello World | Example Site</title>', false)
        ->assertSee('property="og:type" content="article"', false)
        ->assertSee('article:published_time', false)
        ->assertSee('"@type": "BlogPosting"', false)
        ->assertSee('"@type": "BreadcrumbList"', false)
        ->assertSee('rel="canonical" href="https://example.test/blog/hello-world"', false);
});

it('hides draft posts outside the local environment', function () {
    $this->get('http://example.test/blog/unpublished-draft')->assertNotFound();
});

it('returns 404 for unknown pages and slugs', function () {
    $this->get('http://example.test/no-such-page')->assertNotFound();
    $this->get('http://example.test/blog/no-such-post')->assertNotFound();
});

it('generates a sitemap for the site', function () {
    $this->get('http://example.test/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<loc>https://example.test/</loc>', false)
        ->assertSee('<loc>https://example.test/about</loc>', false)
        ->assertSee('<loc>https://example.test/blog/hello-world</loc>', false)
        ->assertDontSee('unpublished-draft');
});

it('applies per-site seo config overrides', function () {
    $this->get('http://example.test/about')
        ->assertSee('name="theme-color" content="#2563eb"', false)
        ->assertSee('og:site_name" content="Example Site"', false);
});

it('builds static output for a site', function () {
    $out = public_path('static/example.test');
    File::deleteDirectory($out);

    try {
        $this->artisan('site:build', ['site' => 'example'])->assertSuccessful();

        expect(File::exists("{$out}/index.html"))->toBeTrue()
            ->and(File::exists("{$out}/about/index.html"))->toBeTrue()
            ->and(File::exists("{$out}/blog/index.html"))->toBeTrue()
            ->and(File::exists("{$out}/blog/hello-world/index.html"))->toBeTrue()
            ->and(File::exists("{$out}/sitemap.xml"))->toBeTrue()
            ->and(File::exists("{$out}/blog/unpublished-draft/index.html"))->toBeFalse();

        expect(File::get("{$out}/blog/hello-world/index.html"))
            ->toContain('"@type": "BlogPosting"');
    } finally {
        File::deleteDirectory($out);
    }
});
