{{--
    Month-grid date picker (single or range). Selected values are mirrored
    into hidden inputs `{name}` and `{name}_end` (range mode), and a
    'calendar:change' event bubbles from the element.

    <x-form.calendar name="broadcast_date" />
    <x-form.calendar name="period" mode="range" />
--}}
@props([
    'name',
    'mode' => 'single', // single | range
])

<div {{ $attributes->class(['calendar']) }} data-calendar data-name="{{ $name }}" data-mode="{{ $mode }}"></div>
