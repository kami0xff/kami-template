{{--
    Full-width mega menu (desktop-first).

    Usage:
        <x-mega-menu>
            <x-slot:trigger>
                <button type="button" class="subnavbar-link">Explore</button>
            </x-slot:trigger>

            <x-mega-menu.grid cols="4">
                <x-mega-menu.column title="Categories">
                    <x-mega-menu.link href="#">Live</x-mega-menu.link>
                </x-mega-menu.column>
                <x-mega-menu.media
                    src="https://…"
                    href="#"
                    caption="Featured show"
                    class="mega-menu-span-2"
                />
            </x-mega-menu.grid>
            <x-mega-menu.separator />
            …
        </x-mega-menu>

    Props:
        hover   — open on hover/focus (default: click)
        contain — wrap slot in .mega-menu-inner max-width rail (default true)
        dim     — page backdrop while open (default: on for click, off for hover)
--}}
@props([
    'hover' => false,
    'contain' => true,
    'dim' => null,
])

@php
    $panelId = 'mega-menu-' . uniqid();
    // Hover + full-screen dim fights the subheader trigger (open/close flash).
    $dim = $dim ?? !$hover;
@endphp

<div
    {{ $attributes->class(['mega-menu']) }}
    data-mega-menu
    @if($hover) data-mega-menu-hover @endif
    @if($dim) data-mega-menu-dim @endif
>
    <span
        class="mega-menu-trigger"
        data-mega-menu-trigger
        aria-haspopup="true"
        aria-expanded="false"
        aria-controls="{{ $panelId }}"
    >
        {{ $trigger }}
    </span>

    <div
        id="{{ $panelId }}"
        class="mega-menu-panel"
        data-mega-menu-panel
        role="navigation"
        aria-label="{{ $attributes->get('aria-label', 'Mega menu') }}"
    >
        @if($contain)
            <div class="mega-menu-inner">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
