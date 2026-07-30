{{--
    Ad / promo block for creator sections.

    Props: href, image, title, text, badge, cta
--}}
@props([
    'href' => '#',
    'image',
    'title' => null,
    'text' => null,
    'badge' => 'Ad',
    'cta' => 'Learn more',
])

<div {{ $attributes->class(['creator-ad']) }}>
    <a class="creator-ad-media" href="{{ $href }}" target="_blank" rel="noopener sponsored">
        @if($badge)
            <span class="creator-ad-badge">{{ $badge }}</span>
        @endif
        <img src="{{ $image }}" alt="{{ $title ?? 'Sponsored' }}" loading="lazy" decoding="async">
    </a>
    @if($title || $text || $cta)
        <div class="creator-ad-meta">
            @if($title)
                <p class="creator-ad-title">{{ $title }}</p>
            @endif
            @if($text)
                <p class="creator-ad-text">{{ $text }}</p>
            @endif
            @if($cta)
                <a class="btn btn-muted btn-sm creator-ad-cta" href="{{ $href }}" target="_blank" rel="noopener sponsored">{{ $cta }}</a>
            @endif
        </div>
    @endif
</div>
