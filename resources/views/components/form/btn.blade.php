@props([
    'type' => 'button',
    'variant' => 'primary',
])

<button
    type="{{ $type }}"
    {{ $attributes->class(['form-btn', 'form-btn-' . $variant]) }}
>{{ $slot }}</button>
