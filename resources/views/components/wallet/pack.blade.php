{{--
    Selectable token pack.

    Props:
        amount   — tokens in the pack
        price    — display price
        value    — form value (defaults to amount)
        name     — radio group name
        checked  — selected
        save     — optional savings label
        badge    — optional corner badge (Popular, Best value)
        hint     — optional footer line
--}}
@props([
    'amount' => 100,
    'price' => '',
    'value' => null,
    'name' => 'pack',
    'checked' => false,
    'save' => null,
    'badge' => null,
    'hint' => null,
])

@php
    $value = $value ?? $amount;
@endphp

<label
    {{ $attributes->class(['wallet-pack', 'is-selected' => $checked]) }}
    data-wallet-pack
    data-pack-amount="{{ $amount }}"
    data-pack-price="{{ $price }}"
>
    <input type="radio" name="{{ $name }}" value="{{ $value }}" @checked($checked)>

    @if($badge)
        <span class="wallet-pack-badge">{{ $badge }}</span>
    @endif

    <span class="wallet-pack-amount">
        <span class="wallet-coin" aria-hidden="true"></span>
        {{ number_format((int) $amount) }}
    </span>

    <span class="wallet-pack-price">{{ $price }}</span>

    @if($save)
        <span class="wallet-pack-save">{{ $save }}</span>
    @endif

    @if($hint)
        <span class="wallet-pack-hint">{{ $hint }}</span>
    @endif
</label>
