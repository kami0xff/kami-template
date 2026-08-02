<?php

use Illuminate\Support\Facades\File;

afterEach(function () {
    File::deleteDirectory(public_path('static/example.test'));
});

it('rewrites content images with webp srcset, dimensions, and priority', function () {
    $response = $this->get('http://example.test/blog/hello-world')->assertOk();

    // WebP fallback src + responsive variants (1600px source: all widths).
    $response->assertSee('src="/images/hello-dashboard-800.webp"', false)
        ->assertSee('/images/hello-dashboard-480.webp 480w', false)
        ->assertSee('/images/hello-dashboard-1600.webp 1600w', false)
        ->assertSee('sizes="', false);

    // Intrinsic dimensions (1600x900 source -> 800x450 fallback), no CLS.
    $response->assertSee('width="800" height="450"', false);

    // First image on the page is the likely LCP: prioritized, not lazy.
    $response->assertSee('fetchpriority="high"', false);

    // Alt text survives the rewrite.
    $response->assertSee('alt="The example dashboard placeholder"', false);
});

it('generates webp variants on demand and serves originals', function () {
    $this->get('http://example.test/images/hello-dashboard-480.webp')
        ->assertOk()
        ->assertHeader('Content-Type', 'image/webp');

    // The variant was written to the static output: next hit is Caddy-only.
    expect(public_path('static/example.test/images/hello-dashboard-480.webp'))->toBeFile();

    $this->get('http://example.test/images/hello-dashboard.png')
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});

it('rejects traversal and non-whitelisted variant widths', function () {
    $this->get('http://example.test/images/../site.php')->assertNotFound();
    $this->get('http://example.test/images/hello-dashboard-999.webp')->assertNotFound();
});

it('pre-generates all image variants during site:build', function () {
    $this->artisan('site:build', ['site' => 'example'])->assertSuccessful();

    foreach ([480, 800, 1600] as $width) {
        expect(public_path("static/example.test/images/hello-dashboard-{$width}.webp"))->toBeFile();
    }

    // Original copied for direct serving (og:image, fallbacks).
    expect(public_path('static/example.test/images/hello-dashboard.png'))->toBeFile();
});
