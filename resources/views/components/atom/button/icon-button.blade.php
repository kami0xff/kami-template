{{--
    Square icon-only button. The `label` prop is required for accessibility
    and doubles as a tooltip unless :tooltip="false".

    <x-atom.button.icon-button label="Add to favorites" variant="muted">
        <x-icons.heart />
    </x-atom.button.icon-button>
--}}
@props([
    'label',
    'variant' => 'muted', // any .btn-* variant
    'size' => '',         // sm | lg
    'tooltip' => true,
    'type' => 'button',
])

<button
    type="{{ $type }}"
    aria-label="{{ $label }}"
    @if($tooltip) data-tooltip="{{ $label }}" @endif
    {{ $attributes->class(['btn', 'btn-icon', "btn-$variant", "btn-$size" => $size]) }}
>
    {{ $slot }}
</button>
