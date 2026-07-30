{{--
    Collapsible Magic Search promo card inside the suggestion panel.

    Props:
        thumb   — image URL for the circular thumbnail
        accent  — optional highlighted phrase (e.g. "3 recherches restantes")
    Slots:
        default — body copy (HTML allowed); wrap accent text in
                  <span class="search-promo-accent">…</span> or pass $accent
--}}
@props([
    'thumb' => null,
    'accent' => null,
])

<div {{ $attributes->merge(['class' => 'search-promo']) }} data-search-promo>
    @if($thumb)
        <div class="search-promo-thumb">
            <img src="{{ $thumb }}" alt="" loading="lazy">
            <span class="search-promo-play" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7L8 5z" />
                </svg>
            </span>
        </div>
    @endif

    <div class="search-promo-body">
        {{ $slot }}
        @if($accent)
            <span class="search-promo-accent">{{ $accent }}</span>
        @endif
    </div>

    <button type="button"
        class="search-promo-toggle"
        data-search-promo-toggle
        aria-label="Toggle Magic Search tip"
        aria-expanded="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <polyline points="6 15 12 9 18 15" />
        </svg>
    </button>
</div>
