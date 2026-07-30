{{-- Single custom link row. --}}
@props([
    'href' => '#',
])

<a href="{{ $href }}" {{ $attributes->class(['creator-link']) }} target="_blank" rel="noopener noreferrer">
    {{ $slot }}
</a>
