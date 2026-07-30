@props([
    'icon' => null,
    'trail' => null,
    'prefix' => null,
    'suffix' => null,
])

<div {{ $attributes->class(['form-input-wrap']) }}>
    @if($icon)
        <span class="form-input-icon">{!! $icon !!}</span>
    @endif

    @if($prefix)
        <span class="form-input-prefix">{!! $prefix !!}</span>
    @endif

    {{ $slot }}

    @if($suffix)
        <span class="form-input-suffix">{!! $suffix !!}</span>
    @endif

    @if(isset($trail))
        {{ $trail }}
    @endif
</div>
