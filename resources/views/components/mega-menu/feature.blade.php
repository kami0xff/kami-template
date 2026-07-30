{{--
    Featured promo card — media + title + supporting text.

    Props:
        src (image), video (optional mp4), href, title, badge, aspect
    Slot: supporting sentence
--}}
@props([
    'src' => null,
    'video' => null,
    'href' => '#',
    'title' => null,
    'badge' => null,
    'aspect' => '16 / 9',
])

<a href="{{ $href }}" {{ $attributes->class(['mega-menu-feature']) }}>
    <div class="mega-menu-feature-frame" style="--mega-menu-aspect: {{ $aspect }}">
        @if($badge)
            <span class="mega-menu-feature-badge">{{ $badge }}</span>
        @endif
        @if($video)
            <video src="{{ $video }}" @if($src) poster="{{ $src }}" @endif muted loop playsinline autoplay preload="metadata"></video>
        @elseif($src)
            <img src="{{ $src }}" alt="" loading="lazy" decoding="async">
        @endif
    </div>
    <div class="mega-menu-feature-body">
        @if($title)
            <div class="mega-menu-feature-title">{{ $title }}</div>
        @endif
        @if(trim($slot) !== '')
            <div class="mega-menu-feature-text">{{ $slot }}</div>
        @endif
    </div>
</a>
