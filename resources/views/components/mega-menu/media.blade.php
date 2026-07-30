{{--
    Image tile (optional link + caption).

    Props:
        src, alt, href, caption, aspect (CSS aspect-ratio, default 16/9)
--}}
@props([
    'src',
    'alt' => '',
    'href' => null,
    'caption' => null,
    'aspect' => '16 / 9',
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->class(['mega-menu-media']) }}
>
    <div class="mega-menu-media-frame" style="--mega-menu-aspect: {{ $aspect }}">
        <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" decoding="async">
    </div>
    @if($caption)
        <div class="mega-menu-media-caption">{{ $caption }}</div>
    @endif
</{{ $tag }}>
