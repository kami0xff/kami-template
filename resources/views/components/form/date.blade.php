{{--
    Date input with calendar popover (Astryx Date Input).

    <x-form.date name="dob" value="1998-04-12" placeholder="Select date" />
--}}
@props([
    'name' => 'date',
    'value' => '',
    'placeholder' => 'Select date',
    'disabled' => false,
    'required' => false,
])

@php
    $current = old($name, $value);
@endphp

<div
    {{ $attributes->class(['form-date-field']) }}
    data-date-field
    data-mode="single"
    data-placeholder="{{ $placeholder }}"
    @if($disabled) data-disabled="true" @endif
>
    <x-form.input-wrap>
        <x-slot:icon>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>
            </svg>
        </x-slot:icon>
        <button
            type="button"
            class="form-input form-date-trigger"
            data-date-trigger
            @disabled($disabled)
            aria-haspopup="dialog"
            aria-expanded="false"
        >
            <span data-date-label class="{{ $current ? '' : 'is-placeholder' }}">
                {{ $current ?: $placeholder }}
            </span>
        </button>
        <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-date-value @if($required) required @endif>
        <x-slot:trail>
            <button type="button" class="form-date-clear" data-date-clear aria-label="Clear date" @if(!$current) hidden @endif>&times;</button>
        </x-slot:trail>
    </x-form.input-wrap>

    <div class="form-date-popover" data-date-popover hidden>
        <x-form.calendar :name="$name . '_cal'" mode="single" />
    </div>
</div>
