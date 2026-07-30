{{--
    Relative time ("3 min ago") that auto-refreshes, with the absolute
    date on hover. Rendering/updating is initRelativeTimestamps() in app.ts.

    <x-timestamp :datetime="'2026-07-24T12:00:00Z'" />
--}}
@props([
    'datetime',
])

@php
    $iso = $datetime instanceof \DateTimeInterface ? $datetime->format(DATE_ATOM) : $datetime;
@endphp

<time {{ $attributes }} datetime="{{ $iso }}" title="{{ $iso }}" data-timestamp>{{ $iso }}</time>
