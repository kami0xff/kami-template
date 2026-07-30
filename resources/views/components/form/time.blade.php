{{--
    Time input (Astryx Time Input — native time control with clock adornment).

    <x-form.time name="starts_at" value="20:30" />
--}}
@props([
    'name' => '',
    'value' => '',
    'disabled' => false,
    'required' => false,
])

<x-form.input-wrap {{ $attributes->only('class')->class(['form-time']) }}>
    <x-slot:icon>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
        </svg>
    </x-slot:icon>
    <x-form.input
        type="time"
        :name="$name"
        :value="$value"
        :disabled="$disabled"
        :required="$required"
        {{ $attributes->except('class') }}
    />
</x-form.input-wrap>
