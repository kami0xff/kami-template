@props([
    'href' => '#',
    'icon' => null,
])

<a {{ $attributes->merge(['class' => 'sidebar-banner'])->except(['href']) }} href="{{ $href }}">
    <span class="sidebar-banner-hover" aria-hidden="true"></span>
    <span class="sidebar-banner-icon" aria-hidden="true">
        <span class="sidebar-banner-pulse"></span>
        @if($icon)
            {{ $icon }}
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 2l2.7 6.5 7 .6-5.3 4.6 1.6 6.9-6-3.6-6 3.6 1.6-6.9L2.3 9.1l7-.6L12 2z" />
            </svg>
        @endif
    </span>
    <span class="sidebar-banner-text">{{ $slot }}</span>
</a>
