{{--
    Link list block (Linktree-style).

    Slot: <a class="creator-link"> or <x-creator.link>
--}}
@props([])

<div {{ $attributes->class(['creator-links']) }}>
    {{ $slot }}
</div>
