{{--
    Post-payment status panel.

    Props:
        state — success | failed | pending
        title
        text
--}}
@props([
    'state' => 'success',
    'title' => null,
    'text' => null,
])

@php
    $defaults = [
        'success' => [
            'title' => 'Payment confirmed',
            'text' => 'Tokens have been added to your wallet. You’re ready to tip, unlock, and go private.',
        ],
        'failed' => [
            'title' => 'Payment failed',
            'text' => 'The provider couldn’t complete this charge. No tokens were added. Try another method.',
        ],
        'pending' => [
            'title' => 'Payment pending',
            'text' => 'We’re waiting on confirmation from the gateway. Your wallet updates automatically when it clears.',
        ],
    ];
    $copy = $defaults[$state] ?? $defaults['success'];
    $title = $title ?? $copy['title'];
    $text = $text ?? $copy['text'];
@endphp

<div {{ $attributes->class(['payment-status', "payment-status--{$state}"]) }} data-payment-status="{{ $state }}">
    <span class="payment-status-icon" aria-hidden="true">
        @if($state === 'success')
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20 6 9 17l-5-5" />
            </svg>
        @elseif($state === 'failed')
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M18 6 6 18M6 6l12 12" />
            </svg>
        @else
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 7v6l3 2" />
            </svg>
        @endif
    </span>

    <h2 class="payment-status-title">{{ $title }}</h2>
    <p class="payment-status-text">{{ $text }}</p>

    @if(trim($slot) !== '')
        <div class="payment-status-actions">
            {{ $slot }}
        </div>
    @endif
</div>
