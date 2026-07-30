{{--
    Simple board pager.

    Props: pages (int), current (int), hrefPattern e.g. '?page={page}' or '#page-{page}'
--}}
@props([
    'pages' => 1,
    'current' => 1,
    'hrefPattern' => '?page={page}',
])

@php
    $pages = max(1, (int) $pages);
    $current = max(1, min($pages, (int) $current));
@endphp

<nav {{ $attributes->class(['forum-pager']) }} data-forum-pager aria-label="Pages">
    @for($i = 1; $i <= $pages; $i++)
        @if($i === $current)
            <span class="is-current" aria-current="page">{{ $i }}</span>
        @else
            <a href="{{ str_replace('{page}', (string) $i, $hrefPattern) }}">{{ $i }}</a>
        @endif
    @endfor
</nav>
