{{--
    Email field — text input with mail adornment (Astryx Text Input pattern).

    <x-form.email name="email" placeholder="you@studio.com" />
--}}
@props([
    'name' => '',
    'value' => '',
    'placeholder' => 'you@example.com',
    'disabled' => false,
    'autofocus' => false,
    'required' => false,
])

<x-form.input-wrap {{ $attributes->only('class') }}>
    <x-slot:icon>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
        </svg>
    </x-slot:icon>
    <x-form.input
        type="email"
        :name="$name"
        :value="$value"
        :placeholder="$placeholder"
        :disabled="$disabled"
        :autofocus="$autofocus"
        :required="$required"
        autocomplete="email"
        inputmode="email"
        {{ $attributes->except('class') }}
    />
</x-form.input-wrap>
