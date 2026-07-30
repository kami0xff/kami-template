{{--
    Mobile bottom navigation.

    <x-bottom-nav>
        <x-bottom-nav.item href="/" label="Home" :active="true" icon="home" />
        <x-bottom-nav.item href="/explore" label="Explore" icon="compass" :badge="3" />
    </x-bottom-nav>
--}}
@props([])

<nav {{ $attributes->class(['bottom-nav']) }} data-bottom-nav aria-label="Primary">
    {{ $slot }}
</nav>
