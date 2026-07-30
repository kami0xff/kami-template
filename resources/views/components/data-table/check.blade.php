{{--
    Optional checkbox cell helper for selectable tables.

    Prefer putting data-dt-row-id on the <tr>; this cell is injected
    automatically only when you build rows manually without the manager
    auto-enhancement. Prefer:

        <tr class="dt__row" data-dt-row-id="{{ $user->id }}">
            <td>...</td>
        </tr>

    With :selectable="true", DataTableManager injects missing checkboxes.
    Use this component when you want the checkbox in Blade markup:

        <x-data-table.check :value="$user->id" />
--}}
@props([
    'value' => '',
    'checked' => false,
    'disabled' => false,
])

<td {{ $attributes->class(['dt__td', 'dt__td--check']) }}>
    <label class="dt__check">
        <input
            type="checkbox"
            value="{{ $value }}"
            data-dt-row-check
            @checked($checked)
            @disabled($disabled)
            aria-label="Select row"
        >
        <span class="dt__check-box" aria-hidden="true"></span>
    </label>
</td>
