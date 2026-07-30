{{--
    Livestream / tipping panel shell.

    <x-tipping.panel>
        <x-slot:tabs>...</x-slot:tabs>
        <x-slot:promo>...</x-slot:promo>
        ...list...
        <x-slot:dock><x-tipping.amounts /></x-slot:dock>
    </x-tipping.panel>
--}}
@props([
    'wide' => false,
])

<div {{ $attributes->class(['tipping', 'tipping--wide' => $wide]) }} data-tipping>
    @isset($tabs)
        <div class="tipping-tabs">
            {{ $tabs }}
        </div>
    @endisset

    @isset($promo)
        <div class="tipping-promo">{{ $promo }}</div>
    @endisset

    @isset($support)
        <div class="tipping-support-head">{{ $support }}</div>
    @endisset

    {{ $slot }}

    @isset($dock)
        <div class="tipping-dock">{{ $dock }}</div>
    @endisset
</div>
