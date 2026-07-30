@props([
    'variant' => 'primary', //primary muted
    'text' => '', //
    'underline' => false,
    'size' => "md", //xs sm md lg xl
    'icon' => null, //icon file path
    'iconPosition' => 'start', //start | end position for the icon
    'href' => '#',
    'rel' => null,
])


@php
    $classes = implode(' ', [
        "link",
        "link-$variant",
        "link-$size",
]);
@endphp


<a
    href="{{$href}}"
    {{$attributes->merge(["class" => $classes])}}
>
    @if($icon && $iconPosition === "start")
        <img src="{{ $icon }}" alt="" aria-hidden="true" />
    @elseif($text)
        <span>{{$text}}</span>
    @else
        {{$slot}}
    @endif

    @if($icon && $iconPosition === "end")
        <img src="{{ $icon }}" alt="" aria-hidden="true" />
    @endif
</a>
