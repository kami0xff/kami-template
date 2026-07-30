{{--
    Generic content card with optional effect skins.

    <x-card>…</x-card>
    <x-card variant="primary-border-glow">…</x-card>
    <x-card variant="corner-glow">…</x-card>

    Variants: primary | primary-border-glow | corner-glow
--}}
@props([
    'variant' => 'primary',
])

@php
    $allowed = ['primary', 'primary-border-glow', 'corner-glow'];
    $variant = in_array($variant, $allowed, true) ? $variant : 'primary';
@endphp

<div {{ $attributes->class(['card', "card-{$variant}"]) }}>
    {{ $slot }}
</div>
