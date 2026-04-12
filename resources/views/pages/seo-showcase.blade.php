@extends('layouts.app')

@section('title', 'SEO Showcase')
@section('meta_description', 'Comprehensive documentation of all SEO features: seo() manager API, config options, SeoService schema builders, breadcrumbs, hreflang, AI content blocks, and on-page checklist.')
@section('canonical', url()->current())
@section('og_image', asset('og-seo-showcase.png'))

@push('seo-pagination')
@php
$seoService = app(\App\Services\SeoService::class);
$hreflangs = $seoService->buildHreflangUrls(url('/seo-showcase'), '/{locale}/seo-showcase');
$breadcrumbItems = [
    ['name' => 'Home', 'url' => url('/')],
    ['name' => 'SEO Showcase', 'url' => url()->current()],
];
$schemas = [
    $seoService->getWebPageSchema(['name' => 'SEO Showcase', 'url' => url()->current(), 'breadcrumb' => $breadcrumbItems]),
    $seoService->getBreadcrumbSchema($breadcrumbItems),
];
@endphp
<x-seo.hreflang :urls="$hreflangs" />
<x-seo.schema :schemas="$schemas" />
@endpush

@section('content')
<div class="seo-showcase" style="background:#111;min-height:100vh;color:#e0e0e0;font-family:system-ui,sans-serif;padding:2rem;max-width:960px;margin:0 auto;">
<h1 style="color:#82b1ff;font-size:2rem;margin-bottom:0.5rem;">SEO Showcase</h1>
    <p style="color:#999;margin-bottom:2rem;">Comprehensive documentation of all SEO features in this template.</p>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">Breadcrumbs Component</h2>
        <x-seo.breadcrumbs :items="$breadcrumbItems" />
        <pre style="background:#1a1a2e;padding:1rem;border-radius:6px;overflow-x:auto;font-size:0.85rem;"><code>&lt;x-seo.breadcrumbs :items="$breadcrumbItems" /&gt;</code></pre>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">Hreflang Component</h2>
        <p style="margin-bottom:1rem;">Push alternate links via <code>seo-pagination</code> stack.</p>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">seo() Manager API</h2>
        <pre style="background:#1a1a2e;padding:1rem;border-radius:6px;overflow-x:auto;font-size:0.85rem;line-height:1.5;"><code>// In a controller
seo()-&gt;title('Blog Post Title')
    -&gt;description('A great article about...')
    -&gt;image(asset('img/post.jpg'), 1200, 630, 'Post image')
    -&gt;article(
        publishedTime: '2025-01-15T10:00:00+00:00',
        modifiedTime: '2025-03-01T14:30:00+00:00',
        author: 'Jane Doe',
        section: 'Technology',
        tags: ['Laravel', 'SEO', 'PHP']
    )
    -&gt;twitterCreator('@janedoe')
    -&gt;keywords('laravel seo, meta tags, structured data');

// Robots control
seo()-&gt;robots('noindex, follow');  // Staging pages
seo()-&gt;robots('index, follow');    // Production default

// Performance hints
seo()-&gt;preconnect('https://cdn.example.com')
    -&gt;preload('/fonts/inter.woff2', 'font', 'font/woff2')
    -&gt;dnsPrefetch('https://analytics.example.com');

// Custom tags
seo()-&gt;tag('fb:app_id', '123456789');
seo()-&gt;rawTag('&lt;link rel="manifest" href="/manifest.json"&gt;');</code></pre>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">config/seo.php</h2>
        <pre style="background:#1a1a2e;padding:1rem;border-radius:6px;overflow-x:auto;font-size:0.85rem;line-height:1.5;"><code>return [
    'title' =&gt; [
        'default' =&gt; env('APP_NAME', 'My App'),
        'suffix' =&gt; env('APP_NAME', 'My App'),
        'separator' =&gt; ' | ',
    ],
    'description' =&gt; env('SEO_DESCRIPTION', ''),
    'keywords' =&gt; env('SEO_KEYWORDS', ''),
    'robots' =&gt; env('SEO_ROBOTS', 'index, follow'),
    'canonical' =&gt; true,
    'og' =&gt; [
        'type' =&gt; 'website',
        'image' =&gt; env('SEO_IMAGE', ''),
        'image_width' =&gt; 1200,
        'image_height' =&gt; 630,
    ],
    'twitter' =&gt; [
        'card' =&gt; 'summary_large_image',
        'site' =&gt; env('TWITTER_SITE', ''),
        'creator' =&gt; env('TWITTER_CREATOR', ''),
    ],
    'organization' =&gt; [
        'name' =&gt; env('APP_NAME', ''),
        'url' =&gt; env('APP_URL', ''),
        'logo' =&gt; env('SEO_ORG_LOGO', ''),
        'same_as' =&gt; [...],
    ],
    'theme_color' =&gt; env('SEO_THEME_COLOR', '#000000'),
    'preconnect' =&gt; ['https://fonts.googleapis.com', ...],
    'feed' =&gt; [
        'enabled' =&gt; env('SEO_FEED_ENABLED', false),
        'url' =&gt; env('SEO_FEED_URL', '/feed'),
    ],
];</code></pre>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">SeoService Schema Builders (16)</h2>
        <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
            <thead>
                <tr style="border-bottom:1px solid #333;">
                    <th style="text-align:left;padding:0.5rem;color:#82b1ff;">Method</th>
                    <th style="text-align:left;padding:0.5rem;color:#82b1ff;">Schema Type</th>
                    <th style="text-align:left;padding:0.5rem;color:#82b1ff;">Key Params</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getHomepageSchema</td><td style="padding:0.5rem;">WebSite</td><td style="padding:0.5rem;">siteName, searchTemplate</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getOrganizationSchema</td><td style="padding:0.5rem;">Organization</td><td style="padding:0.5rem;">override</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getCollectionPageSchema</td><td style="padding:0.5rem;">CollectionPage</td><td style="padding:0.5rem;">name, url, numberOfItems, description</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getItemListSchema</td><td style="padding:0.5rem;">ItemList</td><td style="padding:0.5rem;">name, totalItems, items</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getBreadcrumbSchema</td><td style="padding:0.5rem;">BreadcrumbList</td><td style="padding:0.5rem;">items</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getFaqPageSchema</td><td style="padding:0.5rem;">FAQPage</td><td style="padding:0.5rem;">qaPairs</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getProfilePageSchema</td><td style="padding:0.5rem;">ProfilePage</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getArticleSchema</td><td style="padding:0.5rem;">Article</td><td style="padding:0.5rem;">data, type</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getVideoSchema</td><td style="padding:0.5rem;">VideoObject</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getProductSchema</td><td style="padding:0.5rem;">Product</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getHowToSchema</td><td style="padding:0.5rem;">HowTo</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getReviewSchema</td><td style="padding:0.5rem;">Review</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getEventSchema</td><td style="padding:0.5rem;">Event</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getSoftwareApplicationSchema</td><td style="padding:0.5rem;">SoftwareApplication</td><td style="padding:0.5rem;">data</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getSiteNavigationSchema</td><td style="padding:0.5rem;">SiteNavigationElement</td><td style="padding:0.5rem;">items</td></tr>
                <tr style="border-bottom:1px solid #222;"><td style="padding:0.5rem;">getWebPageSchema</td><td style="padding:0.5rem;">WebPage</td><td style="padding:0.5rem;">data</td></tr>
            </tbody>
        </table>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">AI Content Blocks</h2>
        <p style="margin-bottom:1rem;"><code>&lt;x-seo.content-block page-key="seo-showcase" /&gt;</code> — php artisan seo:generate-page-content</p>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">On-Page SEO Checklist</h2>
        <ul style="color:#b0b0b0;line-height:2;">
            <li>@section('title') — under 60 chars</li>
            <li>@section('meta_description') — 150–160 chars</li>
            <li>@section('canonical'), @section('og_image')</li>
            <li>One &lt;h1&gt;, breadcrumbs, hreflang, schema</li>
            <li>Paginated: link rel="prev" / rel="next"</li>
            <li>Robots meta — index/noindex, follow/nofollow (staging vs production)</li>
            <li>Twitter handles — twitter:site (brand), twitter:creator (author)</li>
            <li>theme-color meta — mobile browser chrome branding</li>
            <li>Preconnect hints — for CDN, fonts, analytics (LCP)</li>
            <li>Article meta — publishedTime, modifiedTime, author, section, tags</li>
            <li>Organization schema — E-E-A-T, sameAs social links</li>
            <li>RSS feed — link rel="alternate" type="application/rss+xml"</li>
        </ul>
    </section>

    <section style="margin-bottom:3rem;">
        <h2 style="color:#82b1ff;font-size:1.5rem;margin-bottom:1rem;">File Structure</h2>
        <pre style="background:#1a1a2e;padding:1rem;border-radius:6px;font-size:0.85rem;"><code>app/Helpers/helpers.php, Services/SeoManager.php, SeoService.php
config/seo.php
resources/views/layouts/app.blade.php, components/seo/*.blade.php</code></pre>
    </section>
</div>
@endsection
