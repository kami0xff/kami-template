@props([
    'variant' => 'flat',
])

<div
    role="separator"
    aria-orientation="horizontal"
    {{ $attributes->class([
        'sidebar-separator',
        "sidebar-separator--{$variant}",
    ]) }}
></div>
