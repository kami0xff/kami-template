@props([
    'name' => '',
    'checked' => false,
    'disabled' => false,
    'label' => '',
])

@php
    $id = $name ?: 'toggle-row-' . Str::random(6);
@endphp

<label {{ $attributes->class([
    'form-toggle-row',
]) }} @if($disabled) aria-disabled="true" @endif for="{{ $id }}">
    <span>
        @if($label)
            {{ $label }}
        @else
            {{ $slot }}
        @endif
    </span>
    <input
        class="form-toggle__input"
        type="checkbox"
        id="{{ $id }}"
        @if($name) name="{{ $name }}" @endif
        value="1"
        @checked(old($name, $checked))
        @disabled($disabled)
    >
    <span class="form-toggle__track">
        <span class="form-toggle__knob"></span>
    </span>
</label>
