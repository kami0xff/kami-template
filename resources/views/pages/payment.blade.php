@extends('layouts.app')

@section('title', 'Buy tokens')

@section('content')
<x-app-shell>
    <x-slot:subnav>
        <a href="{{ route('design') }}#wallet" class="subnavbar-link">Wallet</a>
        <a href="{{ route('payment') }}" class="subnavbar-link active">Buy tokens</a>
        <a href="{{ route('design') }}" class="subnavbar-link">Design system</a>
    </x-slot:subnav>

    <x-payment.checkout :balance="32" provider="Paymentico" stage="select">
        <x-slot:packs>
            <x-wallet.packs>
                <x-wallet.pack :amount="50" price="6,99 €" save="Save 2,01 €" />
                <x-wallet.pack :amount="100" price="11,99 €" save="Save 6,01 €" badge="Popular" :checked="true" />
                <x-wallet.pack :amount="150" price="16,99 €" save="Save 10,01 €" />
                <x-wallet.pack :amount="300" price="29,99 €" save="Save 24,01 €" badge="Best value" />
                <x-wallet.pack :amount="500" price="44,99 €" save="Save 45,01 €" />
                <x-wallet.pack :amount="1000" price="79,99 €" save="Save 100,01 €" />
            </x-wallet.packs>
        </x-slot:packs>

        <x-slot:methods>
            <x-form.radio.group name="payment_method">
                <x-form.radio.item value="card" :checked="true" title="Card" subtitle="Visa, Mastercard, Amex" />
                <x-form.radio.item value="paypal" title="PayPal" subtitle="Pay with your PayPal balance" />
                <x-form.radio.item value="ideal" title="iDEAL / Wero" subtitle="Instant bank transfer" />
                <x-form.radio.item value="crypto" title="Crypto" subtitle="USDT, BTC, ETH, USDC" />
            </x-form.radio.group>
        </x-slot:methods>
    </x-payment.checkout>
</x-app-shell>
@endsection
