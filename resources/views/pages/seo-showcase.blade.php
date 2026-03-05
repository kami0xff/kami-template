{{--
================================================================================
SEO SHOWCASE PAGE
================================================================================
This page demonstrates every SEO component and best practice in the template.
Use it as a reference when building your actual pages.
================================================================================
--}}

@extends('layouts.app')

{{-- ===== 1. PAGE TITLE =====
     - Keep under 60 characters
     - Include primary keyword near the front
     - Include brand name at the end
--}}
@section('title', 'SEO Component Showcase - ' . config('app.name'))

{{-- ===== 2. META DESCRIPTION =====
     - 150-160 characters
     - Include primary + secondary keywords
     - Compelling call-to-action or value proposition
--}}
@section('meta_description', 'A complete reference of all SEO components: JSON-LD schemas, hreflang, canonical URLs, Open Graph, breadcrumbs, and AI-generated content blocks.')

{{-- ===== 3. CANONICAL URL =====
     - Points to the "true" version of this page
     - Prevents duplicate content from ?page=, ?sort=, locale variants, etc.
     - For paginated content, each page gets its own canonical
--}}
@section('canonical', url('/seo-showcase'))

{{-- ===== 4. OPEN GRAPH IMAGE =====
     - 1200x630 recommended
     - Shows in social media shares (Facebook, LinkedIn, Slack, etc.)
--}}
@section('og_image', asset('img/og-default.png'))
@section('og_image_width', '1200')
@section('og_image_height', '630')

{{-- ===== 5. HREFLANG + JSON-LD + PAGINATION LINKS =====
     These go into the <head> via @stack('seo-pagination')
--}}
@push('seo-pagination')
    {{-- Hreflang: tells search engines about locale alternates --}}
    @php
        $hreflangUrls = [
            'en' => url('/seo-showcase'),
            'x-default' => url('/seo-showcase'),
        ];
        foreach (config('locales.priority', []) as $loc) {
            if ($loc !== 'en') {
                $hreflangUrls[$loc] = url("/{$loc}/seo-showcase");
            }
        }
    @endphp
    <x-seo.hreflang :urls="$hreflangUrls" />

    {{-- JSON-LD Schemas --}}
    <x-seo.schema :schemas="[
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'SEO Component Showcase',
            'url' => url('/seo-showcase'),
            'description' => 'Reference page for all SEO components.',
        ],
        app(App\Services\SeoService::class)->getBreadcrumbSchema([
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'SEO Showcase', 'url' => url('/seo-showcase')],
        ]),
    ]" />

    {{-- Pagination prev/next (for paginated pages) --}}
    {{-- Example:
    <link rel="prev" href="{{ url('/items?page=1') }}">
    <link rel="next" href="{{ url('/items?page=3') }}">
    --}}
@endpush


@section('content')
<div style="max-width: 900px; margin: 2rem auto; padding: 0 1rem; font-family: system-ui, sans-serif; color: #e0e0e0; background: #111; min-height: 100vh;">

    <h1 style="border-bottom: 2px solid #333; padding-bottom: 1rem;">SEO Component Showcase</h1>
    <p style="color: #888;">This page demonstrates every SEO pattern in the template. View the Blade source to see the implementation.</p>

    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">1. Breadcrumbs (with BreadcrumbList schema)</h2>
    {{-- ====================================================== --}}

    <x-seo.breadcrumbs :items="[
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Docs', 'url' => url('/docs')],
        ['name' => 'SEO Showcase', 'url' => url('/seo-showcase')],
    ]" />

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem;"><code>&lt;x-seo.breadcrumbs :items="[
    ['name' => 'Home', 'url' => url('/')],
    ['name' => 'Docs', 'url' => url('/docs')],
    ['name' => 'SEO Showcase', 'url' => url('/seo-showcase')],
]" /&gt;</code></pre>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">2. JSON-LD Schema Output</h2>
    {{-- ====================================================== --}}

    <p>Renders <code>&lt;script type="application/ld+json"&gt;</code> blocks in the head. Supports any Schema.org type.</p>

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem;"><code>{{-- In your view --}}
@verbatim
@push('seo-pagination')
<x-seo.schema :schemas="$seoSchemas" />
@endpush

{{-- In your controller --}}
$seoSchemas = [
    $this->seoService->getBreadcrumbSchema([...]),
    $this->seoService->getCollectionPageSchema('My Page', url('/'), 100),
    $this->seoService->getFaqPageSchema([
        ['question' => 'What is this?', 'answer' => 'A demo.'],
    ]),
];
@endverbatim</code></pre>

    <h3>Available Schema Builders (SeoService)</h3>
    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="border-bottom: 1px solid #333;">
                <th style="text-align: left; padding: 0.5rem;">Method</th>
                <th style="text-align: left; padding: 0.5rem;">Schema Type</th>
                <th style="text-align: left; padding: 0.5rem;">Use Case</th>
            </tr>
        </thead>
        <tbody style="color: #aaa;">
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>getHomepageSchema()</code></td>
                <td style="padding: 0.5rem;">WebSite + SearchAction</td>
                <td style="padding: 0.5rem;">Homepage, enables sitelinks search</td>
            </tr>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>getCollectionPageSchema()</code></td>
                <td style="padding: 0.5rem;">CollectionPage</td>
                <td style="padding: 0.5rem;">Category, tag, listing pages</td>
            </tr>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>getItemListSchema()</code></td>
                <td style="padding: 0.5rem;">ItemList</td>
                <td style="padding: 0.5rem;">Paginated grids, search results</td>
            </tr>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>getBreadcrumbSchema()</code></td>
                <td style="padding: 0.5rem;">BreadcrumbList</td>
                <td style="padding: 0.5rem;">Every page with breadcrumbs</td>
            </tr>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>getFaqPageSchema()</code></td>
                <td style="padding: 0.5rem;">FAQPage</td>
                <td style="padding: 0.5rem;">FAQ pages</td>
            </tr>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>getProfilePageSchema()</code></td>
                <td style="padding: 0.5rem;">ProfilePage + Person</td>
                <td style="padding: 0.5rem;">User/model profile pages</td>
            </tr>
        </tbody>
    </table>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">3. Hreflang Links</h2>
    {{-- ====================================================== --}}

    <p>Tells search engines about language/region variants of a page.</p>

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem;"><code>{{-- Build URLs in controller --}}
$hreflangUrls = ['en' => route('page'), 'x-default' => route('page')];
foreach (config('locales.priority') as $loc) {
    if ($loc !== 'en') $hreflangUrls[$loc] = url("/{$loc}/page");
}

{{-- Render in view --}}
@verbatim
@push('seo-pagination')
<x-seo.hreflang :urls="$hreflangUrls" />
@endpush
@endverbatim</code></pre>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">4. AI-Generated SEO Content Blocks</h2>
    {{-- ====================================================== --}}

    <p>Stored in <code>page_seo_content</code> table. Rendered via the content-block component. Supports top/bottom positioning and locale fallback.</p>

    <x-seo.content-block pageKey="home" position="top" />
    <x-seo.content-block pageKey="home" position="bottom" />

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem;"><code>{{-- Top of page --}}
@verbatim<x-seo.content-block pageKey="home" position="top" />@endverbatim

{{-- Bottom of page --}}
@verbatim<x-seo.content-block pageKey="home" position="bottom" />@endverbatim

{{-- Generate content via artisan --}}
php artisan seo:generate-page-content --pages=home
php artisan seo:generate-page-content --translate --priority</code></pre>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">5. Locale Routing Pattern</h2>
    {{-- ====================================================== --}}

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem;"><code>// routes/web.php

// English (default, no prefix)
Route::middleware('detect.locale')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
});

// Localized (/{locale}/...)
$localePattern = implode('|', array_filter(
    array_keys(config('locales.supported', [])),
    fn($l) => $l !== 'en'
));

Route::prefix('{locale}')
    ->where(['locale' => $localePattern])
    ->middleware('set.locale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home.localized');
        Route::get('/about', [PageController::class, 'about'])->name('about.localized');
    });

// In Blade, use localized_route() helper:
// localized_route('home')        → / or /es/
// localized_route('about')       → /about or /fr/about</code></pre>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">6. Middleware Setup</h2>
    {{-- ====================================================== --}}

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem;"><code>// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
    $middleware->alias([
        'set.locale' => \App\Http\Middleware\SetLocale::class,
        'detect.locale' => \App\Http\Middleware\DetectLocale::class,
    ]);
})</code></pre>

    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="border-bottom: 1px solid #333;">
                <th style="text-align: left; padding: 0.5rem;">Middleware</th>
                <th style="text-align: left; padding: 0.5rem;">What it does</th>
            </tr>
        </thead>
        <tbody style="color: #aaa;">
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>detect.locale</code></td>
                <td style="padding: 0.5rem;">Reads <code>Accept-Language</code> header, redirects to <code>/{locale}/</code> on first visit, sets cookie</td>
            </tr>
            <tr style="border-bottom: 1px solid #222;">
                <td style="padding: 0.5rem;"><code>set.locale</code></td>
                <td style="padding: 0.5rem;">Reads <code>{locale}</code> from URL prefix, calls <code>App::setLocale()</code>, strips param from route</td>
            </tr>
        </tbody>
    </table>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">7. On-Page SEO Checklist</h2>
    {{-- ====================================================== --}}

    <div style="background: #1a1a2e; padding: 1.5rem; border-radius: 8px; margin: 1rem 0;">
        <h3 style="margin-top: 0;">Every Page Must Have:</h3>
        <ul style="color: #aaa; line-height: 2;">
            <li><code>@section('title')</code> — unique, under 60 chars, primary keyword first</li>
            <li><code>@section('meta_description')</code> — unique, 150-160 chars, includes CTA</li>
            <li><code>@section('canonical')</code> — the true URL (no params, no duplicates)</li>
            <li><strong>One H1 tag</strong> — contains primary keyword, matches title intent</li>
            <li><strong>Breadcrumbs</strong> — both visual and BreadcrumbList schema</li>
            <li><strong>Hreflang</strong> — for all locale variants (page 1 only for paginated)</li>
            <li><strong>JSON-LD schema</strong> — appropriate type for the page</li>
        </ul>

        <h3>For Paginated Pages, Also Add:</h3>
        <ul style="color: #aaa; line-height: 2;">
            <li><code>&lt;link rel="prev"&gt;</code> and <code>&lt;link rel="next"&gt;</code></li>
            <li>Unique canonical per page (page 1: <code>/items</code>, page 2: <code>/items?page=2</code>)</li>
            <li>Hreflang only on page 1</li>
        </ul>

        <h3>For Social Sharing:</h3>
        <ul style="color: #aaa; line-height: 2;">
            <li><code>@section('og_image')</code> — 1200x630px branded image</li>
            <li>OG title/description can differ from meta if needed for click-through</li>
        </ul>

        <h3>Content Best Practices:</h3>
        <ul style="color: #aaa; line-height: 2;">
            <li>Use <code>&lt;x-seo.content-block&gt;</code> for AI-generated SEO text above/below listings</li>
            <li>Structure content with H2/H3 headings containing secondary keywords</li>
            <li>Internal link to related pages (tags, categories, related items)</li>
            <li>Use translation keys (<code>__('file.key')</code>) for all user-visible text</li>
        </ul>
    </div>


    {{-- ====================================================== --}}
    <h2 style="margin-top: 2rem; color: #82b1ff;">8. File Structure Reference</h2>
    {{-- ====================================================== --}}

    <pre style="background: #1a1a2e; padding: 1rem; border-radius: 8px; overflow-x: auto; font-size: 0.85rem; color: #aaa;"><code>app/
  Console/Commands/
    GeneratePageSeoContent.php    # AI content generation artisan command
  Helpers/
    helpers.php                    # localized_route(), country_flag()
  Http/Middleware/
    DetectLocale.php               # Browser language detection + redirect
    SetLocale.php                  # URL prefix locale setter
  Models/
    PageSeoContent.php             # SEO content with locale fallback
  Services/
    SeoService.php                 # JSON-LD builders, hreflang, OG helpers

config/
  locales.php                      # 50+ locales, priority list, RTL, groups

database/migrations/
  create_page_seo_content_table    # page_key + locale + content + position

lang/
  en/                              # English translations (one file per UI area)
    common.php
    nav.php
    ...
  es/                              # Spanish (same structure)
  fr/                              # French
  ...

resources/views/
  components/seo/
    breadcrumbs.blade.php          # Visual breadcrumbs nav
    content-block.blade.php        # AI-generated SEO text block
    hreflang.blade.php             # &lt;link rel="alternate" hreflang&gt;
    schema.blade.php               # &lt;script type="application/ld+json"&gt;
  layouts/
    app.blade.php                  # Base layout with all SEO head sections</code></pre>

    <p style="color: #666; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #222;">
        Template generated from the PornGuru.cam SEO infrastructure.
        See README.md for setup instructions.
    </p>
</div>
@endsection
