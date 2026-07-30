{{--
    CSS grid inside a mega menu.

    Props:
        cols — 2..6 (default 4)
--}}
@props([
    'cols' => 4,
])

@php
    $cols = max(2, min(6, (int) $cols));
@endphp

<div {{ $attributes->class(['mega-menu-grid', "mega-menu-grid--cols-{$cols}"]) }}>
    {{ $slot }}
</div>
