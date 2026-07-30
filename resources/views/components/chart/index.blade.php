{{--
    Chart card powered by Chart.js (dark theme defaults).

    <x-chart
        type="line"
        title="Revenue"
        meta="Last 7 days"
        :labels="['Mon','Tue','Wed','Thu','Fri','Sat','Sun']"
        :datasets="[
            ['label' => 'Tokens sold', 'data' => [120, 190, 140, 220, 180, 260, 210]],
        ]"
    />

    Types: line | bar | doughnut | pie | polarArea | radar
--}}
@props([
    'type' => 'line',
    'title' => null,
    'meta' => null,
    'labels' => [],
    'datasets' => [],
    'height' => 260,
    'legend' => true,
    'options' => [],
])

@php
    $palette = ['var(--chart-series-1)', 'var(--chart-series-2)', 'var(--chart-series-3)', 'var(--chart-series-4)'];
    $resolved = [];
    foreach ($datasets as $i => $ds) {
        $color = $ds['color'] ?? null;
        // Resolve CSS vars later in JS; pass token names or hex.
        $resolved[] = array_filter([
            'label' => $ds['label'] ?? ('Series ' . ($i + 1)),
            'data' => array_values($ds['data'] ?? []),
            'color' => $color ?? ('series-' . (($i % 4) + 1)),
            'fill' => $ds['fill'] ?? ($type === 'line' ? 'origin' : null),
            'tension' => $ds['tension'] ?? ($type === 'line' ? 0.35 : null),
        ], fn ($v) => $v !== null);
    }

    $config = [
        'type' => $type,
        'labels' => array_values($labels),
        'datasets' => $resolved,
        'legend' => (bool) $legend,
        'options' => $options,
    ];
@endphp

<div {{ $attributes->class(['chart-card']) }} data-chart>
    @if($title || $meta || isset($actions))
        <div class="chart-card-head">
            <div class="chart-card-copy">
                @if($title)<h3 class="chart-card-title">{{ $title }}</h3>@endif
                @if($meta)<p class="chart-card-meta">{{ $meta }}</p>@endif
            </div>
            @isset($actions)
                <div class="chart-card-actions">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="chart-card-body" style="height: {{ (int) $height }}px;">
        <canvas data-chart-canvas aria-label="{{ $title ?: 'Chart' }}" role="img"></canvas>
    </div>

    <script type="application/json" data-chart-config>{!! json_encode($config, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
</div>
