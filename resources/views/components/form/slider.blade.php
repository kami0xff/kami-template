{{--
    Range / value slider.

    <x-form.slider name="tip" :value="65" :min="0" :max="100" />
    <x-form.slider name="age" mode="range" :value-min="20" :value-max="75" />
--}}
@props([
    'name' => '',
    'mode' => 'single', // single | range
    'value' => 50,
    'valueMin' => 20,
    'valueMax' => 75,
    'min' => 0,
    'max' => 100,
    'step' => 1,
])

@php
    $span = max(1, (float) $max - (float) $min);
    if ($mode === 'range') {
        $p1 = (($valueMin - $min) / $span) * 100;
        $p2 = (($valueMax - $min) / $span) * 100;
        $style = '--p1:' . round($p1, 2) . '%;--p2:' . round($p2, 2) . '%;';
    } else {
        $p = (($value - $min) / $span) * 100;
        $style = '--p:' . round($p, 2) . '%;';
    }
@endphp

<div
    {{ $attributes->class(['form-slider']) }}
    style="{{ $style }}"
    data-form-slider
    data-mode="{{ $mode }}"
    data-min="{{ $min }}"
    data-max="{{ $max }}"
    data-step="{{ $step }}"
    @if($name) data-name="{{ $name }}" @endif
    role="group"
    aria-label="{{ $name ?: 'Slider' }}"
>
    <div class="form-slider-track"></div>
    @if($mode === 'range')
        <div class="form-slider-fill form-slider-fill-range"></div>
        <div class="form-slider-thumb form-thumb-1" data-thumb="1" tabindex="0" role="slider" aria-valuemin="{{ $min }}" aria-valuemax="{{ $max }}" aria-valuenow="{{ $valueMin }}"></div>
        <div class="form-slider-thumb form-thumb-2" data-thumb="2" tabindex="0" role="slider" aria-valuemin="{{ $min }}" aria-valuemax="{{ $max }}" aria-valuenow="{{ $valueMax }}"></div>
        @if($name)
            <input type="hidden" name="{{ $name }}_min" value="{{ $valueMin }}" data-slider-input="min">
            <input type="hidden" name="{{ $name }}_max" value="{{ $valueMax }}" data-slider-input="max">
        @endif
    @else
        <div class="form-slider-fill"></div>
        <div class="form-slider-thumb" data-thumb="1" tabindex="0" role="slider" aria-valuemin="{{ $min }}" aria-valuemax="{{ $max }}" aria-valuenow="{{ $value }}"></div>
        @if($name)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-slider-input="value">
        @endif
    @endif
</div>
