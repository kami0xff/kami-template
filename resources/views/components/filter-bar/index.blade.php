{{--
    Filter / facet bar with optional clear-all.

    <x-filter-bar :count="3" clear-label="Clear all">
        <x-atom.pill variant="accent" :active="true">Amateur</x-atom.pill>
        <x-atom.pill>Cosplay</x-atom.pill>
    </x-filter-bar>
--}}
@props([
    'label' => 'Filters',
    'count' => null,
    'clearLabel' => 'Clear all',
    'showClear' => true,
])

<div {{ $attributes->class(['filter-bar']) }} data-filter-bar>
    <div class="filter-bar-head">
        <span class="filter-bar-label">
            {{ $label }}
            @if($count !== null && (int) $count > 0)
                <x-atom.count-badge :count="$count" tone="accent" />
            @endif
        </span>
        @if($showClear)
            <button type="button" class="filter-bar-clear" data-filter-clear>{{ $clearLabel }}</button>
        @endif
    </div>
    <div class="filter-bar-facets" data-filter-facets>
        {{ $slot }}
    </div>
</div>
