{{--
    Default application shell.

    Gives every page the design-system skeleton out of the box:
    navbar → subnav → banner root → optional sidebar → main → footer,
    plus the overlay root used by modals / panels / toasts.

    Usage:
        <x-app-shell>
            ...page content...
        </x-app-shell>

    Slots:
        subnav   — links rendered inside <x-subnavbar>
        sidebar  — an <x-sidebar.sidebar> (enables the with-sidebar layout)
        footer   — custom footer (defaults to <x-footer.footer />)
        overlays — modals and other overlay-root children
    Props:
        brand — passed through to navbar/footer, defaults to app name
--}}
@props([
    'brand' => null,
])

<div class="page-wrapper app-layout {{ isset($sidebar) ? 'app-layout--with-sidebar' : '' }}">
    <x-navbar :brand="$brand" />

    @isset($subnav)
        <x-subnavbar>{{ $subnav }}</x-subnavbar>
    @endisset

    <div class="banner-top-root"></div>

    @isset($sidebar)
        {{ $sidebar }}
        <button class="app-sidebar-backdrop" type="button" aria-label="Close menu" onclick="toggleAppSidebar(false)"></button>
    @endisset

    <main class="main-content">
        {{ $slot }}
    </main>

    @isset($footer)
        {{ $footer }}
    @else
        <x-footer.footer :brand="$brand" />
    @endisset
</div>

{{-- Overlay layer — viewport-spanning root for modals, panels, toasts --}}
<div id="overlay-root" class="overlay-root">
    {{ $overlays ?? '' }}
</div>

{{-- Single bottom dock for people + AI model chats (window.chats / window.aiDock) --}}
<x-chat.dock />
