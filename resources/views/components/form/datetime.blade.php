{{--
    Date + time row (Astryx Date Time Input).

    <x-form.datetime name="starts" date="2026-07-25" time="20:30" />
    Submits: {name}_date + {name}_time
--}}
@props([
    'name' => 'datetime',
    'date' => '',
    'time' => '',
    'disabled' => false,
])

<div {{ $attributes->class(['form-datetime']) }}>
    <x-form.date
        :name="$name . '_date'"
        :value="$date"
        placeholder="Date"
        :disabled="$disabled"
    />
    <x-form.time
        :name="$name . '_time'"
        :value="$time"
        :disabled="$disabled"
    />
</div>
