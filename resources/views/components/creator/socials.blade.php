{{--
    Socials block — row of creator social chips.

    Props:
        icons — icon-only compact layout
    Slot: <x-creator.social> items
--}}
@props([
    'icons' => false,
])

<div {{ $attributes->class(['creator-socials', 'creator-socials--icons' => $icons]) }}>
    {{ $slot }}
</div>
