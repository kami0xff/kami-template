{{--
    Shared layout for static sites. Builds on layouts.app (which renders the
    full SEO head from the seo() manager + per-site config overrides).

    A site can override this file by creating resources/sites/{key}/views/layout.blade.php
    — the site:: namespace checks the site's own views folder first.
--}}
@extends('layouts.app')

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
</style>
@endpush

@section('content')
<header class="site-header">
    <div class="site-header-inner">
        <a class="site-brand" href="{{ $site->url('/') }}">{{ $site->name }}</a>
        <nav class="site-nav">
            <a href="{{ $site->url('/blog') }}">{{ __('Blog') }}</a>
        </nav>
    </div>
</header>

<main class="site-main">
    @yield('site-content')
</main>

<footer class="site-footer">
    <div class="site-footer-inner">
        &copy; {{ date('Y') }} {{ $site->name }}
    </div>
</footer>
@endsection
