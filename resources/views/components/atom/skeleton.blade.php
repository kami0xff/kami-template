@props([
    'variant' => 'line', // line | circle | rect
    'width' => null,
    'height' => null,
])

@php
    $style = 'display:block;' . ($width ? "width:$width;" : '') . ($height ? "height:$height;" : '');
@endphp

<span
    {{ $attributes->class(['skeleton', "skeleton--$variant" => $variant !== 'rect'])->merge(['style' => $style]) }}
    aria-hidden="true"
></span>
