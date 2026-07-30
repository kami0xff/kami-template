@props([
    'id' => null,
])

<div {{ $attributes->class(['tab-root']) }} @if($id) id="{{ $id }}" @endif>
    {{ $slot }}
</div>
