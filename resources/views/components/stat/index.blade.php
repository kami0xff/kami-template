{{--
    <x-stat value="12,480 tk" label="Earnings this week" delta="+18%" direction="up" />
--}}
@props([
    'value',
    'label',
    'delta' => null,
    'direction' => 'up', // up | down (colors the delta)
])

<div {{ $attributes->class(['stat']) }}>
    <span class="stat-value">
        {{ $value }}
        @if($delta)
            <span class="stat-delta stat-delta--{{ $direction }}">{{ $delta }}</span>
        @endif
    </span>
    <span class="stat-label">{{ $label }}</span>
</div>
