{{--
    Count badge — unread / notification quantity.

    Examples:
      x-atom.count-badge :count="3"
      x-atom.count-badge :count="128" max="99"
      x-atom.count-badge :count="1" variant="dot"   (ping only)
--}}
@props([
    'count' => 0,
    'max' => 99,
    'variant' => 'count', // count | dot
    'tone' => 'danger',   // danger | accent | muted
])

@php
    $n = (int) $count;
    $label = $n > $max ? $max . '+' : (string) $n;
@endphp

@if($n > 0)
    <span
        {{ $attributes->class([
            'count-badge',
            'count-badge--dot' => $variant === 'dot',
            'count-badge--' . $tone,
        ]) }}
        @if($variant !== 'dot') aria-label="{{ $n }} new" @endif
    >
        @if($variant !== 'dot'){{ $label }}@endif
    </span>
@endif
