@props([
    'name' => '',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'label' => '',
])

@php
    $id = $name ? $name . '-' . $value : 'checkbox-' . Str::random(6);
@endphp

<label {{ $attributes->class([
    'checkbox',
    'checkbox--disabled' => $disabled,
]) }} for="{{ $id }}">
    <input
        class="checkbox__input"
        type="checkbox"
        id="{{ $id }}"
        @if($name) name="{{ $name }}" @endif
        value="{{ $value }}"
        @checked($checked)
        @disabled($disabled)
    >
    <span class="checkbox__box">
        <svg class="checkbox__check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </span>
    @if($label)
        <span class="checkbox__label">{{ $label }}</span>
    @elseif($slot->isNotEmpty())
        <span class="checkbox__label">{{ $slot }}</span>
    @endif
</label>
