{{--
    Static table wrapper — put <thead>/<tbody> in the slot.

    <x-table striped hover>
        <thead><tr><th>Model</th><th>Status</th></tr></thead>
        <tbody>…</tbody>
    </x-table>
--}}
@props([
    'striped' => false,
    'hover' => false,
    'compact' => false,
])

<table {{ $attributes->class(['table', 'table--striped' => $striped, 'table--hover' => $hover, 'table--compact' => $compact]) }}>
    {{ $slot }}
</table>
