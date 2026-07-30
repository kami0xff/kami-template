@props([
    'type' => 'text',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'disabled' => false,
    'autofocus' => false,
    'required' => false,
])

<input
    type="{{ $type }}"
    @if($name) name="{{ $name }}" id="{{ $name }}" @endif
    value="{{ old($name, $value) }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @disabled($disabled)
    @if($autofocus) autofocus @endif
    @if($required) required @endif
    {{ $attributes->class(['form-input']) }}
>
