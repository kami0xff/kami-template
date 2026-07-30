@props([
    'method' => 'POST',
    'action' => '',
])

@php
    $actualMethod = strtoupper($method);
    $formMethod = in_array($actualMethod, ['GET', 'POST']) ? $actualMethod : 'POST';
@endphp

<form
    method="{{ $formMethod }}"
    @if($action) action="{{ $action }}" @endif
    {{ $attributes->class(['form']) }}
>
    @if($actualMethod !== 'GET')
        @csrf
    @endif

    @if(! in_array($actualMethod, ['GET', 'POST']))
        @method($actualMethod)
    @endif

    {{ $slot }}
</form>
