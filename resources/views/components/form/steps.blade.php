@props([
    'bar' => false,
])

<div {{ $attributes->class([
    'form-steps',
    'form-steps-bar' => $bar,
]) }}>
    {{ $slot }}
</div>
