@props([
    'name' => '',
    'label' => '',
    'required' => false,
    'optional' => false,
    'help' => '',
    'link' => null,
    'linkText' => '',
    'counter' => null,
    'state' => null,
])

@php
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $hasError = $name && $errorBag->has($name);
    $stateClass = match ($state) {
        'focus' => 'is-focus',
        'valid' => 'is-valid',
        'error' => 'is-error',
        'checking' => 'is-checking',
        default => null,
    };
@endphp

<div {{ $attributes->class([
    'form-field',
    'form-field--error' => $hasError,
    $stateClass => $stateClass,
]) }}>
    @if($label)
        <label class="form-label" @if($name) for="{{ $name }}" @endif>
            {{ $label }}
            @if($required)
                <span class="form-label-required" aria-hidden="true">*</span>
            @endif
            @if($optional)
                <span class="form-label-optional">{{ is_string($optional) ? $optional : '(optional)' }}</span>
            @endif
            @if($link)
                <a href="{{ $link }}" class="form-label-link">{{ $linkText ?: $link }}</a>
            @endif
            @if($counter)
                <span class="form-counter">{!! $counter !!}</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($hasError)
        <p class="form-error">{{ $errorBag->first($name) }}</p>
    @endif

    @if($help && ! $hasError)
        <p class="form-help">{!! $help !!}</p>
    @endif
</div>
