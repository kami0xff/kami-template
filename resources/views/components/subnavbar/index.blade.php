{{--
    Secondary header row under the navbar.

    On small screens the navbar search collapses; a magnifying-glass control
    on the right opens the search overlay over this bar (see SubnavSearchManager).
--}}
@props([
    'searchToggle' => true,
])

<div {{ $attributes->class(['subnavbar']) }} data-subnavbar>
    <div class="subnavbar-links">
        {{ $slot }}
    </div>

    @if($searchToggle)
        <button
            type="button"
            class="subnavbar-search-toggle"
            data-subnav-search-toggle
            aria-label="Open search"
            aria-expanded="false"
            aria-controls="nav-search-layer"
            title="Search"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
        </button>
    @endif
</div>
