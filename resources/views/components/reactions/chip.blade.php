{{--
    Reaction / emote chip — Unicode emoji OR custom image/GIF.

    Props:
        name, count, active
        emoji  — unicode fallback
        src    — custom emote image (png/webp/gif)
        label  — shortcode / accessible name (e.g. kamiHype)
        animated — hint for GIF/webp animation class
--}}
@props([
    'name',
    'count' => 0,
    'active' => false,
    'emoji' => null,
    'src' => null,
    'label' => null,
    'animated' => false,
])

@php
    $label = $label ?? $name;
    $aria = trim(($label ?: $emoji ?: $name) . ' ' . $count);
@endphp

<button
    type="button"
    {{ $attributes->class(['reaction-chip']) }}
    data-reaction="{{ $name }}"
    @if($emoji) data-reaction-emoji="{{ $emoji }}" @endif
    @if($src) data-reaction-src="{{ $src }}" @endif
    @if($label) data-reaction-label="{{ $label }}" @endif
    @if($animated || str_ends_with(strtolower((string) $src), '.gif')) data-reaction-animated @endif
    aria-pressed="{{ $active ? 'true' : 'false' }}"
    aria-label="{{ $aria }}"
    title=":{{ $label }}:"
>
    <span class="reaction-chip-emote" aria-hidden="true">
        @if($src)
            <img
                class="reaction-emote-img {{ ($animated || str_ends_with(strtolower((string) $src), '.gif')) ? 'reaction-emote-img--animated' : '' }}"
                src="{{ $src }}"
                alt=""
                width="20"
                height="20"
                loading="lazy"
                decoding="async"
                draggable="false"
            >
        @else
            <span class="reaction-chip-emoji">{{ $emoji }}</span>
        @endif
    </span>
    <span class="reaction-chip-count" data-reaction-count>{{ $count }}</span>
</button>
