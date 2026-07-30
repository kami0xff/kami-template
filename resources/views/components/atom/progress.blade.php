@props([
    'value' => 0,
    'max' => 100,
    'variant' => null, // success | warning | danger
])

@php
    $percent = $max > 0 ? min(100, max(0, ($value / $max) * 100)) : 0;
@endphp

<div
    {{ $attributes->class(['progress', "progress--$variant" => $variant]) }}
    role="progressbar"
    aria-valuenow="{{ $value }}"
    aria-valuemin="0"
    aria-valuemax="{{ $max }}"
>
    <div class="progress-fill" style="width: {{ $percent }}%"></div>
</div>
