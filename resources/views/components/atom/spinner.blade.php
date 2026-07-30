@props([
    'size' => 'md', // sm | md | lg
])

<span
    {{ $attributes->class(['spinner', "spinner--$size" => $size !== 'md']) }}
    role="status"
    aria-label="Loading"
></span>
