{{--
    Search bar with optional suggestion panel (stripchat-style).

    Props:
        placeholder — input placeholder
        name        — input name (default "q")
        filter      — show the trailing filter-sliders tool button

    Slots:
        chip    — trailing action pill, e.g. <x-search.chip count="3">Magic Search</x-search.chip>
        panel   — suggestion dropdown content, built from
                  <x-search.promo> + <x-search.examples>/<x-search.example>
                  + <x-search.section> + <x-search.item>

    Behavior (SearchSuggestManager):
        focus/typing opens the panel · Esc or outside click closes it ·
        ↑ ↓ move over items, Enter activates · "/" focuses the first
        search on the page · input dispatches "search:query" CustomEvents.
--}}
@props([
    'placeholder' => 'Search…',
    'name' => 'q',
    'filter' => false,
])

<div {{ $attributes->merge(['class' => 'search']) }} data-search>
    <div class="search-bar">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
        </svg>

        <input type="text" class="search-input" name="{{ $name }}" placeholder="{{ $placeholder }}"
            autocomplete="off" spellcheck="false" data-search-input>

        <div class="search-tools">
            @if($filter)
                <button type="button" class="search-tool" data-search-filter aria-label="Search filters">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="4" y1="6" x2="14" y2="6" /><circle cx="17" cy="6" r="2" />
                        <line x1="10" y1="18" x2="20" y2="18" /><circle cx="7" cy="18" r="2" />
                        <line x1="4" y1="12" x2="20" y2="12" />
                    </svg>
                </button>
            @endif
            {{ $chip ?? '' }}
        </div>
    </div>

    @isset($panel)
        <div class="search-panel" data-search-panel hidden>
            {{ $panel }}
        </div>
    @endisset
</div>
