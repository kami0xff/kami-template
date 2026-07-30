{{--
    Video tile (autoplay muted loop by default — decorative preview).

    Props:
        src, poster, href, caption, aspect, controls
--}}
@props([
    'src',
    'poster' => null,
    'href' => null,
    'caption' => null,
    'aspect' => '16 / 9',
    'controls' => false,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->class(['mega-menu-video']) }}
>
    <div class="mega-menu-video-frame" style="--mega-menu-aspect: {{ $aspect }}">
        <video
            src="{{ $src }}"
            @if($poster) poster="{{ $poster }}" @endif
            @if($controls) controls @else muted loop playsinline autoplay @endif
            preload="metadata"
        ></video>
    </div>
    @if($caption)
        <div class="mega-menu-media-caption">{{ $caption }}</div>
    @endif
</{{ $tag }}>
