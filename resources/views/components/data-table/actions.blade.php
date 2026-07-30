{{--
    Compact quick-action cluster for a data-table cell.

    <x-data-table.actions>
        <x-atom.button.button size="sm" variant="muted" text="Edit" />
        <x-atom.button.button size="sm" variant="danger" text="Ban" />
    </x-data-table.actions>
--}}
@props([
    'align' => 'right', // left | center | right
])

<div {{ $attributes->class(['dt__actions', 'dt__actions--' . $align]) }} data-dt-actions>
    {{ $slot }}
</div>
