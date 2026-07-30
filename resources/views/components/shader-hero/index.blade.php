{{--
    SEO-first marketing hero with optional WebGL liquid-aurora backdrop.

    The heading, lede, and CTAs are real DOM text (crawlable).
    The canvas is decorative only (aria-hidden) and falls back to CSS.

    Props:
        eyebrow / title / lede — marketing copy (title becomes <h1> by default)
        headingLevel — 1|2 (use 2 if the page already has an h1)
        primaryHref / primaryLabel / secondaryHref / secondaryLabel
        align — start|center
        variant — chrome | glow | studio | aurora | plasma | mesh | silk | ember
        schema — optional JSON-LD array (WebPage / WebSite / etc.)
--}}
@props([
    'id' => 'shader-hero',
    'eyebrow' => 'Shaders for the web',
    'title' => 'Motion that still ranks.',
    'lede' => 'A GPU backdrop for marketing pages — with real HTML copy, semantic headings, and a static fallback when WebGL or motion is unavailable.',
    'headingLevel' => 1,
    'primaryHref' => '#',
    'primaryLabel' => 'Get started',
    'secondaryHref' => null,
    'secondaryLabel' => 'See how it works',
    'align' => 'start',
    'variant' => 'chrome',
    'note' => null,
    'schema' => [],
])

@php
    $level = (int) $headingLevel === 2 ? 2 : 1;
    $headingTag = $level === 2 ? 'h2' : 'h1';
    $titleId = $id . '-title';
@endphp

@if(!empty($schema))
    <x-seo.schema :schemas="$schema" />
@endif

<section
    id="{{ $id }}"
    {{ $attributes->class([
        'shader-hero',
        'shader-hero--center' => $align === 'center',
        "shader-hero--variant-{$variant}",
    ]) }}
    data-shader-hero
    data-variant="{{ $variant }}"
    aria-labelledby="{{ $titleId }}"
>
    <div class="shader-hero-stage" aria-hidden="true">
        <canvas class="shader-hero-canvas" data-shader-hero-canvas></canvas>
    </div>
    <div class="shader-hero-scrim" aria-hidden="true"></div>

    <div class="shader-hero-inner">
        @if($eyebrow)
            <p class="shader-hero-eyebrow">{{ $eyebrow }}</p>
        @endif

        <{{ $headingTag }} class="shader-hero-title" id="{{ $titleId }}">{!! $title !!}</{{ $headingTag }}>

        @if($lede)
            <p class="shader-hero-lede">{{ $lede }}</p>
        @endif

        @if(isset($slot) && $slot->isNotEmpty())
            {{ $slot }}
        @else
            <div class="shader-hero-actions">
                <a class="shader-hero-cta shader-hero-cta--primary" href="{{ $primaryHref }}">{{ $primaryLabel }}</a>
                @if($secondaryHref)
                    <a class="shader-hero-cta shader-hero-cta--ghost" href="{{ $secondaryHref }}">{{ $secondaryLabel }}</a>
                @endif
            </div>
        @endif

        @if($note)
            <p class="shader-hero-note">{{ $note }}</p>
        @endif
    </div>

    <noscript>
        <style>.shader-hero-canvas{display:none!important}</style>
    </noscript>
</section>
