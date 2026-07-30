@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'fill',
])

@php
    $classes = implode(' ', array_filter([
        'liquid-metal-button',
        "liquid-metal-button--{$variant}",
    ]));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span class="liquid-metal-button-border" aria-hidden="true"></span>
        <span class="liquid-metal-button-content">
            {{ $slot }}
        </span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span class="liquid-metal-button-border" aria-hidden="true"></span>
        <span class="liquid-metal-button-content">
            {{ $slot }}
        </span>
    </button>
@endif
