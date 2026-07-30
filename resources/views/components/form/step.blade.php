@props([
    'label' => '',
    'number' => null,
    'done' => false,
    'active' => false,
])

<div {{ $attributes->class([
    'form-step',
    'done' => $done,
    'active' => $active,
]) }}>
    <div class="form-step-num">
        @if($done)
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
        @else
            {{ $number ?? $slot }}
        @endif
    </div>
    @if($label)
        <div class="form-step-label">{{ $label }}</div>
    @endif
</div>
