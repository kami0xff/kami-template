{{-- panel.blade.php --}}
@props([
    'id',
    'width' => '280px',
    'highlight' => null,
    'animation' => 'fade',
    'elevation' => 'high',
    'border' => 'subtle',
])

<div {{ $attributes->class([
    'panel',
    'panel--highlight-' . $highlight => $highlight,
    'panel--anim-' . $animation,
    'panel--elevation-' . $elevation,
    'panel--border-' . $border,
]) }} id="{{ $id }}" style="width: {{ $width }}">

    @if($border === 'frost')
        <div class="panel-glow-layer">
            <div class="panel-glow-blob panel-glow-blob--1"></div>
            <div class="panel-glow-blob panel-glow-blob--2"></div>
            <div class="panel-glow-blob panel-glow-blob--3"></div>
        </div>
        <div class="panel-noise"></div>
    @endif

    @if($highlight)
        <div class="panel-highlight"></div>
    @endif

    <div class="panel-content">
        {{ $slot }}
    </div>
</div>
