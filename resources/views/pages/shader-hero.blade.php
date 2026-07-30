@extends('layouts.app')

@section('title', 'Shader Hero — SEO marketing backdrop')
@section('meta_description', 'Flat WebGL chrome hero with crawlable HTML copy, semantic headings, reduced-motion fallback, and JSON-LD for marketing landings.')

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
@endpush

@section('content')
@php
    $schemas = [[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Shader Hero — SEO marketing backdrop',
        'description' => 'Flat WebGL chrome hero with crawlable HTML copy and progressive enhancement.',
        'url' => url('/shader-hero'),
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => config('app.name', 'Kami Template'),
            'url' => url('/'),
        ],
    ]];
@endphp

<style>
    .shader-hero-demo-page { background: #05060a; min-height: 100vh; }
    .shader-hero-demo-page .shader-hero { min-height: 100svh; }
    .shader-hero-demo-note {
        max-width: 640px;
        margin: 0 auto;
        padding: 56px 24px 96px;
        color: rgba(245,245,245,.55);
        font-family: Inter, system-ui, sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }
    .shader-hero-demo-note h2 {
        color: #f5f5f5;
        font-size: 15px;
        font-weight: 500;
        letter-spacing: -0.02em;
        margin: 0 0 14px;
    }
    .shader-hero-demo-note code {
        font-size: 12px;
        background: rgba(245,245,245,.06);
        padding: 1px 6px;
        border-radius: 4px;
    }
    .shader-hero-demo-note ul { margin: 12px 0 0; padding-left: 1.15em; }
    .shader-hero-demo-note a { color: #f0c9a0; text-decoration: none; }
    .shader-hero-demo-note a:hover { text-decoration: underline; }
</style>

<div class="shader-hero-demo-page">
    <x-shader-hero
        id="marketing-hero"
        variant="chrome"
        align="center"
        eyebrow="Liquid chrome · WebGL"
        title="Full home automation <em>made agentic.</em>"
        lede="Flat SDF chrome tiles with blue / amber rim light and soft bloom — a 3D illusion without a rotating mesh. Real HTML on top."
        primary-href="{{ route('design') }}#shader-hero"
        primary-label="Open in design kit"
        secondary-href="{{ route('design') }}#shader-hero"
        secondary-label="All variants →"
        note="variant=chrome · prefers-reduced-motion → static CSS"
        :schema="$schemas"
    />

    <aside class="shader-hero-demo-note">
        <h2>Chrome recipe</h2>
        <ul>
            <li>Union of rounded-square SDFs in a diamond cluster (flat fragment shader)</li>
            <li>Fake normals from the SDF gradient → liquid silver + Fresnel rim</li>
            <li>Cool electric blue / warm amber lights + soft bloom bleed</li>
            <li>Fine grain · SEO copy stays in the DOM · no 3D mesh</li>
        </ul>
        <p style="margin-top:18px;">
            <code>&lt;x-shader-hero variant="chrome" /&gt;</code>
            · <a href="{{ route('design') }}#shader-hero">/design#shader-hero</a>
        </p>
    </aside>
</div>
@endsection
