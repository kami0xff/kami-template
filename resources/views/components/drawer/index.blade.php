{{--
    Overlay panel. Open with window.drawers.open('id'),
    close via the × button, backdrop click, or Escape.

    Props:
        side     — right | left | bottom
        variant  — edge (slide from screen edge) | float (fade in, inset, rounded)
        backdrop — null (auto: dim for edge, clear for float) | true | false

    <x-drawer id="filters-drawer" side="right" title="Filters">
        …content…
    </x-drawer>
    <x-drawer id="messages-drawer" side="right" variant="float" title="Messages">
        …content…
    </x-drawer>
    <button onclick="drawers.open('filters-drawer')">Filters</button>
--}}
@props([
    'id',
    'side' => 'right', // right | left | bottom
    'variant' => 'edge', // edge | float
    'title' => null,
    'backdrop' => null, // null = auto (edge dimmed, float clear) | true | false
])

@php
    $variant = in_array($variant, ['edge', 'float'], true) ? $variant : 'edge';
    $withBackdrop = $backdrop === null
        ? $variant !== 'float'
        : filter_var($backdrop, FILTER_VALIDATE_BOOLEAN);
@endphp

<div
    class="drawer-overlay{{ $withBackdrop ? '' : ' drawer-overlay--clear' }}"
    id="{{ $id }}"
    data-drawer-variant="{{ $variant }}"
>
    <aside
        {{ $attributes->class([
            'drawer',
            "drawer--$side",
            'drawer--float' => $variant === 'float',
        ]) }}
        role="dialog"
        aria-modal="true"
        @if($title) aria-label="{{ $title }}" @endif
    >
        @if($title)
            <div class="drawer-head">
                <span>{{ $title }}</span>
                <button type="button" class="drawer-close" aria-label="Close" onclick="window.drawers.close('{{ $id }}')">&times;</button>
            </div>
        @endif
        <div class="drawer-body">
            {{ $slot }}
        </div>
    </aside>
</div>
