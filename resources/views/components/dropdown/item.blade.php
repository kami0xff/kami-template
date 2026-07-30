@props([
    'href' => null,   // renders <a> when set, <button> otherwise
    'danger' => false,
    'hint' => null,   // right-aligned hint, e.g. a keyboard shortcut
])

@php
    $classes = 'menu-item' . ($danger ? ' menu-item--danger' : '');
@endphp

@if($href)
<a href="{{ $href }}" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
@else
<button type="button" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
@endif
    @isset($icon)
        <span class="menu-item-icon" aria-hidden="true">{{ $icon }}</span>
    @endisset

    <span>{{ $slot }}</span>

    @if($hint)
        <span class="menu-item-hint">{{ $hint }}</span>
    @endif
@if($href)
</a>
@else
</button>
@endif
