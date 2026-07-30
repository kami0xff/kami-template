{{-- Text link row for mega-menu columns. Optional $icon slot. --}}
@props([
    'href' => '#',
    'hint' => null,
])

<a href="{{ $href }}" {{ $attributes->class(['mega-menu-link']) }}>
    @isset($icon)
        <span class="mega-menu-link-icon" aria-hidden="true">{{ $icon }}</span>
    @endisset
    <span>{{ $slot }}</span>
    @if($hint)
        <span class="mega-menu-link-hint">{{ $hint }}</span>
    @endif
</a>
