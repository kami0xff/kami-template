{{--
    Date range input with calendar popover (Astryx Date Range Input).

    <x-form.date-range name="period" start="2026-07-01" end="2026-07-15" />
    Submits: {name} + {name}_end
--}}
@props([
    'name' => 'period',
    'start' => '',
    'end' => '',
    'placeholder' => 'Select date range',
    'disabled' => false,
])

@php
    $startVal = old($name, $start);
    $endVal = old($name . '_end', $end);
    $label = ($startVal && $endVal)
        ? $startVal . ' → ' . $endVal
        : ($startVal ?: null);
@endphp

<div
    {{ $attributes->class(['form-date-field', 'form-date-field--range']) }}
    data-date-field
    data-mode="range"
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
            <span data-date-label class="{{ $label ? '' : 'is-placeholder' }}">
                {{ $label ?: $placeholder }}
            </span>
        </button>
        <input type="hidden" name="{{ $name }}" value="{{ $startVal }}" data-date-value>
        <input type="hidden" name="{{ $name }}_end" value="{{ $endVal }}" data-date-value-end>
        <x-slot:trail>
            <button type="button" class="form-date-clear" data-date-clear aria-label="Clear dates" @if(!$label) hidden @endif>&times;</button>
        </x-slot:trail>
    </x-form.input-wrap>

    <div class="form-date-popover" data-date-popover hidden>
        <x-form.calendar :name="$name . '_cal'" mode="range" />
    </div>
</div>
