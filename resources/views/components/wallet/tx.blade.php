{{--
    Single wallet transaction row.

    Props:
        type   — credit | debit
        title  — primary label
        meta   — date / method / note
        amount — signed display string or absolute number (prefix from type)
--}}
@props([
    'type' => 'credit',
    'title' => '',
    'meta' => null,
    'amount' => '',
])

@php
    $isCredit = $type === 'credit';
    $display = is_numeric($amount)
        ? (($isCredit ? '+' : '−') . number_format(abs((int) $amount)))
        : $amount;
@endphp

<li {{ $attributes->class(['wallet-tx', $isCredit ? 'wallet-tx--credit' : 'wallet-tx--debit']) }}>
    <span class="wallet-tx-icon" aria-hidden="true">
        @if($isCredit)
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
        @else
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14" />
            </svg>
        @endif
    </span>

    <div class="wallet-tx-main">
        <div class="wallet-tx-title">{{ $title }}</div>
        @if($meta)
            <div class="wallet-tx-meta">{{ $meta }}</div>
        @endif
    </div>

    <div class="wallet-tx-amount">{{ $display }}</div>
</li>
