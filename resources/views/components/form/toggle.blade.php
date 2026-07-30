@props([
    'name' => '',
    'checked' => false,
    'disabled' => false,
    'label' => '',
])

@php
    $id = $name ?: 'toggle-' . Str::random(6);
@endphp

<label {{ $attributes->class([
    'form-toggle',
    'form-toggle--disabled' => $disabled,
]) }} for="{{ $id }}">
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
    @if($label)
        <span class="form-toggle__label">{{ $label }}</span>
    @elseif($slot->isNotEmpty())
        <span class="form-toggle__label">{{ $slot }}</span>
    @endif
</label>
