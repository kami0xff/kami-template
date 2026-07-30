{{--
    Numeric input with steppers.

    <x-form.number name="tokens" :value="100" :min="0" :max="10000" :step="50" />
--}}
@props([
    'name',
    'value' => 0,
    'min' => null,
    'max' => null,
    'step' => 1,
])

<div {{ $attributes->class(['form-number']) }}>
    <button type="button" class="form-number-btn" data-number-dec aria-label="Decrease">&minus;</button>
    <input
        type="number"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        step="{{ $step }}"
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
    >
    <button type="button" class="form-number-btn" data-number-inc aria-label="Increase">+</button>
</div>
