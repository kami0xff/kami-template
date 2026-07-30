{{--
    Token pack grid. Slot receives <x-wallet.pack> children.
--}}
@props([
    'label' => null,
])

<div {{ $attributes->class(['wallet-packs-wrap']) }}>
    @if($label)
        <div class="select-col-label">{{ $label }}</div>
    @endif
    <div class="wallet-packs" data-wallet-packs>
        {{ $slot }}
    </div>
</div>
