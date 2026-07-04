{{--
    Shared layout for static sites. Builds on layouts.app (which renders the
    full SEO head from the seo() manager + per-site config overrides).

    A site can override this file by creating resources/sites/{key}/views/layout.blade.php
    — the site:: namespace checks the site's own views folder first.
--}}
@extends('layouts.app')

{{-- Per-site Umami analytics (cookieless — no consent banner needed).
     Each site is its own Umami "website" so stats never mix. Production
     only, so local/dev builds don't pollute the numbers. --}}
@if(!empty($site->analytics['website_id']))
    @production
    @push('head')
        <script defer
            src="{{ $site->analytics['src'] ?? config('services.umami.src', 'https://cloud.umami.is/script.js') }}"
            data-website-id="{{ $site->analytics['website_id'] }}"></script>
    @endpush
    @endproduction
@endif

@push('styles')
<style>
    :root {
        --site-accent: {{ config('seo.theme_color', '#2563eb') }};
        --site-bg: #ffffff;
        --site-text: #1f2937;
        --site-muted: #6b7280;
        --site-border: #e5e7eb;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--site-bg);
        color: var(--site-text);
        line-height: 1.7;
    }
    .site-header {
        border-bottom: 1px solid var(--site-border);
        padding: 1rem 1.5rem;
    }
    .site-header-inner, .site-main, .site-footer-inner {
        max-width: 720px;
        margin: 0 auto;
    }
    .site-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .site-brand {
        font-weight: 700;
        font-size: 1.125rem;
        color: var(--site-text);
        text-decoration: none;
    }
    .site-nav a {
        color: var(--site-muted);
        text-decoration: none;
        margin-left: 1.25rem;
        font-size: 0.9375rem;
    }
    .site-nav a:hover { color: var(--site-accent); }
    .site-main { padding: 2.5rem 1.5rem 4rem; }
    .site-main h1 { font-size: 2rem; line-height: 1.25; letter-spacing: -0.02em; }
    .site-main a { color: var(--site-accent); }
    .site-main img { max-width: 100%; height: auto; border-radius: 0.5rem; }
    .site-main pre {
        background: #f3f4f6;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        font-size: 0.875rem;
    }
    .site-main code { background: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875em; }
    .site-main pre code { background: none; padding: 0; }
    .site-main blockquote {
        border-left: 3px solid var(--site-accent);
        margin-left: 0;
        padding-left: 1.25rem;
        color: var(--site-muted);
    }
    .site-main table { border-collapse: collapse; width: 100%; }
    .site-main th, .site-main td { border: 1px solid var(--site-border); padding: 0.5rem 0.75rem; text-align: left; }
    .post-meta { color: var(--site-muted); font-size: 0.875rem; margin-bottom: 2rem; }
    .post-list { list-style: none; padding: 0; margin: 0; }
    .post-list-item { padding: 1.5rem 0; border-bottom: 1px solid var(--site-border); }
    .post-list-item h2 { margin: 0 0 0.375rem; font-size: 1.25rem; }
    .post-list-item h2 a { text-decoration: none; color: var(--site-text); }
    .post-list-item h2 a:hover { color: var(--site-accent); }
    .post-list-item p { margin: 0.375rem 0 0; color: var(--site-muted); }
    .post-tags { margin-top: 2rem; }
    .post-tag {
        display: inline-block;
        background: #f3f4f6;
        color: var(--site-muted);
        border-radius: 9999px;
        padding: 0.125rem 0.75rem;
        font-size: 0.8125rem;
        margin-right: 0.375rem;
    }
    .breadcrumbs-list { list-style: none; display: flex; flex-wrap: wrap; gap: 0.375rem; padding: 0; margin: 0 0 1.5rem; font-size: 0.875rem; }
    .breadcrumbs-item { color: var(--site-muted); }
    .breadcrumbs-link { color: var(--site-muted); text-decoration: none; }
    .breadcrumbs-link:hover { color: var(--site-accent); }
    .breadcrumbs-separator { margin-left: 0.375rem; }
    .site-footer {
        border-top: 1px solid var(--site-border);
        padding: 1.5rem;
        color: var(--site-muted);
        font-size: 0.875rem;
    }
    .toc {
        background: #f9fafb;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        padding: 1rem 1rem 1rem 2.25rem;
        font-size: 0.9375rem;
    }
    .toc a { text-decoration: none; }
    .heading-permalink {
        color: var(--site-border);
        text-decoration: none;
        margin-left: 0.375rem;
        font-size: 0.8em;
    }
    h2:hover .heading-permalink, h3:hover .heading-permalink { color: var(--site-accent); }
    .tldr {
        background: #eff6ff;
        border-left: 3px solid var(--site-accent);
        border-radius: 0 0.5rem 0.5rem 0;
        padding: 1rem 1.25rem;
        margin: 1.5rem 0;
    }
    .tldr ul { margin: 0.5rem 0 0; padding-left: 1.25rem; }
    .post-faq details {
        border-bottom: 1px solid var(--site-border);
        padding: 0.75rem 0;
    }
    .post-faq summary { cursor: pointer; font-weight: 600; }
    .post-faq p { margin: 0.75rem 0 0.25rem; }
    .post-sources { font-size: 0.9375rem; }
    .post-quiz {
        background: #f9fafb;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        padding: 1.25rem 1.5rem;
        margin: 2rem 0;
    }
    .quiz-question { font-weight: 600; }
    .quiz-options { display: grid; gap: 0.5rem; }
    .quiz-option {
        text-align: left;
        padding: 0.625rem 1rem;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        background: #fff;
        cursor: pointer;
        font: inherit;
    }
    .quiz-option:hover:not(:disabled) { border-color: var(--site-accent); }
    .quiz-option.is-correct { border-color: #16a34a; background: #f0fdf4; }
    .quiz-option.is-wrong { border-color: #dc2626; background: #fef2f2; }
    .quiz-explanation { color: var(--site-muted); font-size: 0.9375rem; }
    .author-box {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        background: #f9fafb;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        padding: 1.25rem 1.5rem;
        margin-top: 2.5rem;
    }
    .author-avatar { border-radius: 9999px; }
    .author-title { color: var(--site-muted); margin-left: 0.5rem; font-size: 0.875rem; }
    .author-box p { margin: 0.5rem 0; font-size: 0.9375rem; color: var(--site-muted); }
    .author-links a { margin-right: 0.75rem; font-size: 0.875rem; }
    .related-posts { margin-top: 2.5rem; }
    .related-posts h3 { margin: 0 0 0.25rem; font-size: 1.0625rem; }
    .cluster-hub {
        background: #f9fafb;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        font-size: 0.9375rem;
    }
    .cluster-spokes { margin-top: 2.5rem; }
    .cluster-spokes h3 { margin: 0 0 0.25rem; font-size: 1.0625rem; }
    .lead-form {
        background: #eff6ff;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 2.5rem;
    }
    .lead-form h2 { margin: 0 0 0.5rem; font-size: 1.25rem; }
    .lead-form p { margin: 0 0 1rem; color: var(--site-muted); font-size: 0.9375rem; }
    .lead-form form { display: grid; gap: 0.625rem; }
    .lead-form input, .lead-form textarea {
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--site-border);
        border-radius: 0.5rem;
        font: inherit;
        width: 100%;
    }
    .lead-form button {
        justify-self: start;
        background: var(--site-accent);
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        padding: 0.625rem 1.5rem;
        font: inherit;
        font-weight: 600;
        cursor: pointer;
    }
    .lead-form .lf-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
    .lead-thanks { color: #16a34a; font-weight: 600; }
    .lead-error { color: #dc2626; }
</style>
@endpush

@section('content')
<header class="site-header">
    <div class="site-header-inner">
        <a class="site-brand" href="{{ $site->url('/') }}">{{ $site->name }}</a>
        <nav class="site-nav">
            <a href="{{ $site->url('/blog') }}">{{ __('Blog') }}</a>
            @if($site->search)
                <a href="{{ $site->url('/search') }}">{{ __('Search') }}</a>
            @endif
        </nav>
    </div>
</header>

{{-- data-pagefind-body scopes the search index to page content (skips
     header/footer chrome) for sites with search enabled. --}}
<main class="site-main" data-pagefind-body>
    @yield('site-content')
</main>

<footer class="site-footer">
    <div class="site-footer-inner">
        &copy; {{ date('Y') }} {{ $site->name }}
    </div>
</footer>
@endsection
