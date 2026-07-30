@props([
    'name' => '',
    'checked' => false,
    'disabled' => false,
    'label' => '',
    'large' => false,
])

@php
    $id = $name ?: 'check-' . Str::random(6);
@endphp

<label {{ $attributes->class([
    'form-check',
    'form-check-lg' => $large,
]) }} @if($disabled) aria-disabled="true" @endif>
    <input
        type="checkbox"
        id="{{ $id }}"
        @if($name) name="{{ $name }}" @endif
        value="1"
        @checked(old($name, $checked))
        @disabled($disabled)
    >
    <span class="form-check-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
    </span>
    @if($label)
        {{ $label }}
    @elseif($slot->isNotEmpty())
        {{ $slot }}
    @endif
</label>
