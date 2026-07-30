{{--
    Board index list shell (multiple index-threads).
--}}
@props([])

<div {{ $attributes->class(['forum-index']) }} data-forum-index>
    {{ $slot }}
</div>
