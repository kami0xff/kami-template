{{-- Titled link column inside a mega-menu grid. --}}
@props([
    'title' => null,
])

<div {{ $attributes->class(['mega-menu-column']) }}>
    @if($title)
        <p class="mega-menu-column-title">{{ $title }}</p>
    @endif
    {{ $slot }}
</div>
