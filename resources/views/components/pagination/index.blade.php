{{--
    Generic pagination with style variants.

    Props:
        current  — current page (1-based)
        last     — last page
        url      — URL pattern with {page} placeholder
        window   — pages shown around current (default 1)
        variant  — pill | boxed | simple | compact  (compact = pill + sm)
        size     — md | sm

    <x-pagination :current="4" :last="12" url="?page={page}" />
    <x-pagination variant="boxed" size="sm" :current="2" :last="8" url="?page={page}" />
    <x-pagination variant="simple" :current="3" :last="20" url="?page={page}" />
--}}
@props([
    'current' => 1,
    'last' => 1,
    'url' => '?page={page}',
    'window' => 1,
    'variant' => 'pill',
    'size' => 'md',
])

@php
    $pageUrl = fn (int $p) => str_replace('{page}', (string) $p, $url);
    $isCompact = $variant === 'compact';
    $variantClass = match ($isCompact ? 'pill' : $variant) {
        'boxed' => 'pagination--boxed',
        'simple' => 'pagination--simple',
        default => null,
    };
    $sizeClass = ($size === 'sm' || $isCompact) ? 'pagination--sm' : null;

    $pages = [];
    if ($variant !== 'simple') {
        for ($i = 1; $i <= $last; $i++) {
            if ($i === 1 || $i === $last || abs($i - $current) <= $window) {
                $pages[] = $i;
            } elseif (end($pages) !== '…') {
                $pages[] = '…';
            }
        }
    }
@endphp

@if($last > 1)
<nav {{ $attributes->class(['pagination', $variantClass, $sizeClass]) }} aria-label="Pagination">
    @if($current > 1)
        <a class="pagination-item" href="{{ $pageUrl($current - 1) }}" rel="prev" aria-label="Previous page">&lsaquo;</a>
    @else
        <span class="pagination-item pagination-item--disabled" aria-disabled="true">&lsaquo;</span>
    @endif

    @if($variant === 'simple')
        <span class="pagination-status">Page <strong>{{ $current }}</strong> of <strong>{{ $last }}</strong></span>
    @else
        @foreach($pages as $page)
            @if($page === '…')
                <span class="pagination-ellipsis" aria-hidden="true">…</span>
            @elseif($page === $current)
                <span class="pagination-item pagination-item--current" aria-current="page">{{ $page }}</span>
            @else
                <a class="pagination-item" href="{{ $pageUrl($page) }}">{{ $page }}</a>
            @endif
        @endforeach
    @endif

    @if($current < $last)
        <a class="pagination-item" href="{{ $pageUrl($current + 1) }}" rel="next" aria-label="Next page">&rsaquo;</a>
    @else
        <span class="pagination-item pagination-item--disabled" aria-disabled="true">&rsaquo;</span>
    @endif
</nav>
@endif
