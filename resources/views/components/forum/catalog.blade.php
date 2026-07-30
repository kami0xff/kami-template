{{--
    Catalog grid shell. Slot: catalog-thread cards.
--}}
@props([])

<div {{ $attributes->class(['forum-catalog']) }} data-forum-catalog>
    {{ $slot }}
</div>
