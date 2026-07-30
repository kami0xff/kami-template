@props([
    'variant' => 'primary', //primary danger success warning muted back money light
    'title' => '',
    'text' => '', //
    'size' => '', //xs sm md lg xl
    'icon' => null, //icon file path
    'iconPosition' => 'start', //start | end position for the icon
    'disabled' => false, //disabled button state
    'loading' => false, //displays a spinner and disables the button to prevent multiple submits
    'type' => 'button', //submit | button | reset
    'fullWidth' => false,
])


@php
    $classes = implode(' ', array_filter([
        "btn",
        "btn-$variant",
        $size ? "btn-$size" : "",
        $fullWidth ? "btn-block" : "",
        $loading ? "btn-loading" : "",
        $disabled ? "btn-disabled" : ""
    ]));
@endphp

<button type="{{$type}}"
        {{$attributes->merge(['class' => $classes])}}
        @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <span class="btn-spinner" aria-hidden="true"></span>
    @elseif($icon && $iconPosition === "start")
        <img src="{{ $icon }}" alt="" aria-hidden="true" />
    @endif

    @if($text)
        <span>{{$text}}</span>
    @else
        {{$slot}}
    @endif

    @if(!$loading && $icon && $iconPosition === "end")
        <img src="{{ $icon }}" alt="" aria-hidden="true" />
    @endif

</button>
