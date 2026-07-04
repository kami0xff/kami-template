<?php

use Illuminate\Support\Facades\File;

it('injects the per-site Umami snippet in production only', function () {
    // Not in testing/local...
    $this->get('http://example.test/')
        ->assertOk()
        ->assertDontSee('data-website-id', false);

    // ...but baked into production output (which is what site:build runs as).
    $this->app['env'] = 'production';

    $this->get('http://example.test/')
        ->assertOk()
        ->assertSee('data-website-id="00000000-0000-0000-0000-000000000000"', false);

    $this->app['env'] = 'testing';
});

it('renders the branded minimal error page for site 404s', function () {
    $this->get('http://example.test/no-such-page')
        ->assertNotFound()
        ->assertSee('error-code', false)      // minimal error layout, not Laravel's default
        ->assertSee('Example Site')           // branded with this site's name only
        ->assertSee('This page could not be found.');
});

it('serves a per-site robots.txt pointing at the site sitemap', function () {
    $this->get('http://example.test/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Sitemap: https://example.test/sitemap.xml');
});

it('serves the search page with the Pagefind UI mount', function () {
    $this->get('http://example.test/search')
        ->assertOk()
        ->assertSee('id="search"', false)
        ->assertSee('/pagefind/pagefind-ui.js', false)
        ->assertSee('noindex', false); // search pages must not be indexed
});

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

it('renders rich article sections from front matter', function () {
    $response = $this->get('http://example.test/blog/hello-world')->assertOk();

    // TL;DR box
    $response->assertSee('TL;DR')
        ->assertSee('The filename is the URL slug');

    // Table of contents with heading anchors
    $response->assertSee('class="toc"', false)
        ->assertSee('href="#how-the-front-matter-feeds-seo"', false);

    // FAQ section + FAQPage schema
    $response->assertSee('Frequently asked questions')
        ->assertSee('Where do blog posts live?')
        ->assertSee('"@type": "FAQPage"', false);

    // Quiz
    $response->assertSee('Quick check')
        ->assertSee('What determines a blog post&#039;s URL?', false)
        ->assertSee('class="quiz-option"', false);

    // Related articles (explicit slug from front matter)
    $response->assertSee('Related articles')
        ->assertSee('/blog/writing-content', false);

    // References
    $response->assertSee('References')
        ->assertSee('developers.google.com/search', false);
});

it('renders the E-E-A-T author box and Person schema', function () {
    $this->get('http://example.test/blog/hello-world')
        ->assertOk()
        ->assertSee('class="author-box"', false)
        ->assertSee('Founder &amp; Web Performance Consultant', false)
        ->assertSee('"@type": "Person"', false)
        ->assertSee('twitter.com/janedoe', false)
        ->assertSee('rel="author"', false);
});

it('hides draft posts outside the local environment', function () {
    $this->get('http://example.test/blog/unpublished-draft')->assertNotFound();
});

it('returns 404 for unknown pages and slugs', function () {
    $this->get('http://example.test/no-such-page')->assertNotFound();
    $this->get('http://example.test/blog/no-such-post')->assertNotFound();
});

it('serves an RSS feed of published posts', function () {
    $this->get('http://example.test/feed.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->assertSee('<title>Hello World</title>', false)
        ->assertSee('<link>https://example.test/blog/hello-world</link>', false)
        ->assertDontSee('Unpublished Draft');
});

it('advertises the feed in the page head', function () {
    $this->get('http://example.test/about')
        ->assertSee('type="application/rss+xml"', false)
        ->assertSee('example.test/feed.xml', false);
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
            ->and(File::exists("{$out}/feed.xml"))->toBeTrue()
            ->and(File::exists("{$out}/blog/unpublished-draft/index.html"))->toBeFalse();

        expect(File::get("{$out}/blog/hello-world/index.html"))
            ->toContain('"@type": "BlogPosting"');
    } finally {
        File::deleteDirectory($out);
    }
});
