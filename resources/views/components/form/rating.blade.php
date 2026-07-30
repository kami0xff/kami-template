{{--
    Star rating.

    <x-form.rating name="score" :value="4" />
    <x-form.rating :value="3" size="sm" :readonly="true" />
--}}
@props([
    'name' => '',
    'value' => 0,
    'max' => 5,
    'size' => 'md', // md | sm
    'readonly' => false,
])

@php $current = (int) $value; @endphp

<div
    {{ $attributes->class(['form-rating', 'sm' => $size === 'sm']) }}
    data-form-rating
    data-readonly="{{ $readonly ? '1' : '0' }}"
    @if($name) data-name="{{ $name }}" @endif
    role="{{ $readonly ? 'img' : 'radiogroup' }}"
    aria-label="Rating"
>
    @for($i = 1; $i <= $max; $i++)
        <button
            type="button"
            @class(['form-rating-star', 'filled' => $i <= $current])
            data-rating-star
            data-value="{{ $i }}"
            @if($readonly) tabindex="-1" @endif
            aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}"
        >
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2.5l2.9 6.1 6.7.7-5 4.6 1.4 6.6L12 17.8 5.9 20.5 7.3 13.9l-5-4.6 6.7-.7L12 2.5z"/>
            </svg>
        </button>
    @endfor
    @if($name)
        <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-rating-input>
    @endif
</div>
