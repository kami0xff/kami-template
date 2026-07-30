@props([
    'name' => 'pin',
    'length' => 6,
    'values' => [],
])

<div {{ $attributes->class(['form-pin']) }} data-pin-input>
    @for($i = 0; $i < $length; $i++)
        <input
            class="form-pin-cell"
            type="text"
            inputmode="numeric"
            maxlength="1"
            name="{{ $name }}[]"
            value="{{ $values[$i] ?? '' }}"
            autocomplete="one-time-code"
        >
    @endfor
</div>
