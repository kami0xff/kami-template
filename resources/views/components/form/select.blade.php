@props([
    'name' => '',
    'options' => [],
    'selected' => '',
    'placeholder' => '',
    'disabled' => false,
])

<div class="form-select-wrap">
    <select
        @if($name) name="{{ $name }}" id="{{ $name }}" @endif
        @disabled($disabled)
        {{ $attributes->class(['form-input', 'form-select']) }}
    >
        @if($placeholder)
            <option value="" disabled @selected(! old($name, $selected))>{{ $placeholder }}</option>
        @endif

        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(old($name, $selected) == $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
</div>
