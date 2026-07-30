{{--
    Wallet transaction list. Slot receives <x-wallet.tx> children.
--}}
@props([
    'title' => 'Recent activity',
    'action' => null,
])

<section {{ $attributes->class(['wallet-history']) }} data-wallet-history>
    <div class="wallet-history-head">
        <h3 class="wallet-history-title">{{ $title }}</h3>
        @if($action)
            {{ $action }}
        @endif
    </div>

    <ul class="wallet-history-list">
        {{ $slot }}
    </ul>
</section>
