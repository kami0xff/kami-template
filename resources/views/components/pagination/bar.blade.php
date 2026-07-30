{{--
    Table / list pagination bar — meta + per-page selector + page controls.

    <x-pagination.bar
        :current="4" :last="12" :total="284" :per-page="24"
        :per-page-options="[12, 24, 48, 96]"
        url="?page={page}&per=24"
        per-page-url="?per={per}&page=1"
        variant="boxed"
        size="sm"
    />
--}}
@props([
    'current' => 1,
    'last' => 1,
    'total' => 0,
    'perPage' => 24,
    'perPageOptions' => [12, 24, 48, 96],
    'url' => '?page={page}',
    'perPageUrl' => '?per={per}&page=1',
    'variant' => 'boxed',
    'size' => 'sm',
    'window' => 1,
])

@php
    $from = $total > 0 ? (($current - 1) * $perPage) + 1 : 0;
    $to = $total > 0 ? min($current * $perPage, $total) : 0;
    $perPageHref = fn (int $n) => str_replace('{per}', (string) $n, $perPageUrl);
@endphp

<div {{ $attributes->class(['pagination-bar']) }}>
    <div class="pagination-bar-meta">
        @if($total > 0)
            Showing <strong>{{ number_format($from) }}–{{ number_format($to) }}</strong>
            of <strong>{{ number_format($total) }}</strong>
        @else
            No results
        @endif
    </div>

    <div class="pagination-bar-controls">
        <label class="pagination-bar-size">
            <span>Rows</span>
            <select aria-label="Rows per page" onchange="if (this.value) location.href = this.value">
                @foreach($perPageOptions as $n)
                    <option value="{{ $perPageHref((int) $n) }}" @selected((int) $n === (int) $perPage)>
                        {{ $n }}
                    </option>
                @endforeach
            </select>
        </label>

        <x-pagination
            :current="$current"
            :last="max(1, (int) $last)"
            :url="$url"
            :window="$window"
            :variant="$variant"
            :size="$size"
        />
    </div>
</div>
