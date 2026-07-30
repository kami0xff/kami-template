{{--
    Accordion group — stacks collapsibles; optional exclusive mode.

    Usage:
        <x-accordion>
            <x-collapsible title="One">…</x-collapsible>
            <x-collapsible title="Two">…</x-collapsible>
        </x-accordion>

        <x-accordion exclusive variant="panel">
            …
        </x-accordion>

    Props:
        exclusive — only one item open at a time (default false)
        flush     — shared outer border, no gaps
        variant   — forwarded hint class on the group (panel | ghost)
--}}
@props([
    'exclusive' => false,
    'flush' => false,
    'variant' => null,
])

<div
    {{ $attributes->class([
        'accordion',
        'accordion--flush' => $flush,
        'accordion--' . $variant => $variant,
    ]) }}
    data-accordion
    @if($exclusive) data-accordion-exclusive @endif
>
    {{ $slot }}
</div>
