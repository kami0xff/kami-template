@props([
    'cols' => 2,
])

<div {{ $attributes->class(['form-row']) }} style="--form-row-cols: {{ $cols }}">
    {{ $slot }}
</div>
