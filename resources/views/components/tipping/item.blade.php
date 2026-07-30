{{--
    Tip menu activity row.

    <x-tipping.item label="Show feet 👣" :tokens="49" />
--}}
@props([
    'label' => '',
    'tokens' => 0,
    'emoji' => null,
    'active' => false,
])

<button
    type="button"
    {{ $attributes->class(['tipping-item', 'is-active' => $active]) }}
    data-tipping-item
    data-tokens="{{ (int) $tokens }}"
>
    <span class="tipping-item-label">
        @if($emoji)<span class="tipping-item-emoji" aria-hidden="true">{{ $emoji }}</span>@endif
        <span>{{ $label }}</span>
    </span>
    <span class="tipping-item-price">{{ number_format((int) $tokens) }}</span>
</button>
