{{--
    Horizontal scroll rail with arrow paddles — the category-bar pattern.
    Put pills, chips, or cards in the default slot.

    <x-overflow-list>
        <x-atom.pill>Categories</x-atom.pill>
        …
    </x-overflow-list>
--}}
@props([
    'arrows' => true,
])

<div {{ $attributes->class(['overflow-list']) }} data-overflow-list>
    @if($arrows)
        <button type="button" class="overflow-list-arrow overflow-list-arrow--prev" data-overflow-prev aria-label="Scroll back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
        </button>
    @endif

    <div class="overflow-list-track">
        {{ $slot }}
    </div>

    @if($arrows)
        <button type="button" class="overflow-list-arrow overflow-list-arrow--next" data-overflow-next aria-label="Scroll forward">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
        </button>
    @endif
</div>
