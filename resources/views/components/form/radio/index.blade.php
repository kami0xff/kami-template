@props([
    'name' => '',
    'value' => '',
    'checked' => false,
    'disabled' => false,
    'label' => '',
])

@php
    $id = $name . '-' . Str::slug($value);
    $isChecked = old($name, $checked ? $value : null) == $value;
@endphp

<label {{ $attributes->class([
    'form-radio',
    'form-radio--disabled' => $disabled,
]) }} for="{{ $id }}">
    <input
        class="form-radio__input"
        type="radio"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($isChecked)
        @disabled($disabled)
    >
    <span class="form-radio__circle"></span>
    @if($label)
        <span class="form-radio__label">{{ $label }}</span>
    @elseif($slot->isNotEmpty())
        <span class="form-radio__label">{{ $slot }}</span>
    @endif
</label>
