@props([
    'variant' => 'neutral',  // neutral | outline | accent | money
    'size' => '',            // '' (default 32px) | sm (26px)
    'href' => null,          // renders <a> when set, <button> otherwise
    'count' => null,         // muted counter suffix (e.g. live counts)
    'dot' => false,          // leading live dot
    'active' => false,
])

@php
    $classes = implode(' ', array_filter([
        'pill',
        $variant !== 'neutral' ? "pill--$variant" : '',
        $size ? "pill--$size" : '',
        $active ? 'pill--active' : '',
    ]));
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
<button type="button" {{ $attributes->merge(['class' => $classes]) }}>
@endif
    @if($dot)
        <span class="pill-dot" aria-hidden="true"></span>
    @endif

    @isset($icon)
        <span class="pill-icon" aria-hidden="true">{{ $icon }}</span>
    @endisset

    <span>{{ $slot }}</span>

    @if($count !== null)
        <span class="pill-count">{{ is_numeric($count) ? number_format($count) : $count }}</span>
    @endif
@if($href)
</a>
@else
</button>
@endif
