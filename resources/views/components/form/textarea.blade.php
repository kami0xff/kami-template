@props([
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'rows' => 4,
    'disabled' => false,
])

<textarea
    @if($name) name="{{ $name }}" id="{{ $name }}" @endif
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @disabled($disabled)
    {{ $attributes->class(['form-input', 'form-textarea']) }}
>{{ old($name, $value) }}</textarea>
