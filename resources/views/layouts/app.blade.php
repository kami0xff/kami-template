<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(in_array(app()->getLocale(), config('locales.rtl', [])))dir="rtl"@endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ===== TITLE ===== --}}
    {{-- Every page must set @section('title'). Keep under 60 characters. --}}
    <title>@yield('title', config('app.name', 'Kami Template'))</title>

    {{-- ===== META DESCRIPTION ===== --}}
    {{-- Every page must set @section('meta_description'). Keep 150-160 chars. --}}
    <meta name="description" content="@yield('meta_description', 'Default meta description for your site. Override this per-page.')">

    {{-- ===== CANONICAL URL ===== --}}
    {{-- Prevents duplicate content from URL params, pagination, locale variants. --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- ===== OPEN GRAPH ===== --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', config('app.name')))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description', ''))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
    <meta property="og:image:width" content="@yield('og_image_width', '1200')">
    <meta property="og:image:height" content="@yield('og_image_height', '630')">
    @endif
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">

    {{-- ===== TWITTER CARD ===== --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('og_title', View::yieldContent('title', config('app.name')))">
    <meta name="twitter:description" content="@yield('og_description', View::yieldContent('meta_description', ''))">
    @hasSection('og_image')
    <meta name="twitter:image" content="@yield('og_image')">
    @endif

    {{-- ===== HREFLANG + JSON-LD + PAGINATION ===== --}}
    {{-- This stack is where pages push <link rel="alternate" hreflang="...">, --}}
    {{-- <link rel="prev/next">, and <script type="application/ld+json">. --}}
    @stack('seo-pagination')

    {{-- ===== FAVICON ===== --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- ===== CSS ===== --}}
    @vite(['resources/css/app.css'])

    {{-- ===== EXTRA HEAD CONTENT ===== --}}
    {{-- For additional JSON-LD, preloads, or page-specific assets. --}}
    @stack('head')

    {{-- ===== GOOGLE ANALYTICS ===== --}}
    @if(config('services.google.analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google.analytics_id') }}');
    </script>
    @endif
</head>
<body>
    @yield('content')
</body>
</html>
