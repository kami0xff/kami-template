{{--
    Wallet balance card.

    Props:
        balance — token count (int|string)
        label   — heading above the amount
        unit    — e.g. "tokens"
        meta    — optional helper line
--}}
@props([
    'balance' => 0,
    'label' => 'Token balance',
    'unit' => 'tokens',
    'meta' => null,
])

<div {{ $attributes->class(['wallet-balance']) }} data-wallet-balance>
    <div class="wallet-balance-label">{{ $label }}</div>

    <div class="wallet-balance-row">
        <span class="wallet-coin" aria-hidden="true"></span>
        <span class="wallet-balance-value" data-wallet-balance-value>{{ number_format((int) $balance) }}</span>
        <span class="wallet-balance-unit">{{ $unit }}</span>
    </div>

    @if($meta)
        <p class="wallet-balance-meta">{{ $meta }}</p>
    @endif

    @if(trim($slot) !== '')
        <div class="wallet-balance-actions">
            {{ $slot }}
        </div>
    @endif
</div>
