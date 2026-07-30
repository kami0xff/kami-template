{{--
    Payment checkout shell.

    Flow (UI only):
      1. select  — packs + methods + summary CTA
      2. gateway — third-party host (iframe / redirect)
      3. status  — success | failed | pending after provider callback

    Props:
        stage    — select | gateway | status
        provider — gateway display name
        balance  — current wallet balance (shown in summary)
--}}
@props([
    'stage' => 'select',
    'provider' => 'Paymentico',
    'balance' => 0,
])

<div
    {{ $attributes->class(['payment-page']) }}
    data-payment-checkout
    data-stage="{{ $stage }}"
    data-provider="{{ $provider }}"
>
    <header class="payment-page-header">
        <h1 class="payment-page-title">Buy tokens</h1>
        <p class="payment-page-lead">
            Pick a pack, continue to our payment partner, then we credit your wallet when they confirm.
        </p>
    </header>

    {{-- Stage: pack selection --}}
    <div class="payment-stage" data-payment-stage="select" @if($stage !== 'select') hidden @endif>
        <div class="payment-layout">
            <div class="payment-panel">
                <h2 class="payment-panel-title">Choose a pack</h2>
                <p class="payment-panel-hint">Larger packs unlock better rates. Prices include tax where applicable.</p>

                {{ $packs ?? '' }}

                @isset($methods)
                    <h2 class="payment-panel-title" style="margin-top:var(--space2)">Payment method</h2>
                    <p class="payment-panel-hint">You’ll finish on the provider’s secure page — we never see card numbers.</p>
                    <div class="payment-methods">
                        {{ $methods }}
                    </div>
                @endisset
            </div>

            <aside class="payment-panel payment-summary">
                <h2 class="payment-panel-title">Order summary</h2>

                <div>
                    <div class="payment-summary-row">
                        <span>Current balance</span>
                        <strong data-payment-current-balance>{{ number_format((int) $balance) }} tokens</strong>
                    </div>
                    <div class="payment-summary-row">
                        <span>Pack</span>
                        <strong data-payment-summary-pack>—</strong>
                    </div>
                    <div class="payment-summary-row payment-summary-total">
                        <span>Total</span>
                        <strong data-payment-summary-price>—</strong>
                    </div>
                </div>

                <p class="payment-secure-note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    <span>Charged once by {{ $provider }}. No recurring fees. Tokens appear after the gateway confirms payment.</span>
                </p>

                <button type="button" class="btn btn-money btn-block" data-payment-continue>
                    Continue to secure payment
                </button>

                {{ $summaryExtra ?? '' }}
            </aside>
        </div>
    </div>

    {{-- Stage: third-party gateway --}}
    <div class="payment-stage" data-payment-stage="gateway" @if($stage !== 'gateway') hidden @endif>
        <div class="payment-panel">
            <h2 class="payment-panel-title">Secure payment — {{ $provider }}</h2>
            <p class="payment-panel-hint">
                Completing <strong data-payment-summary-pack>—</strong> for
                <strong data-payment-summary-price>—</strong>. Stay on this page until the provider finishes.
            </p>

            <x-payment.gateway :provider="$provider">
                {{ $gateway ?? '' }}
            </x-payment.gateway>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space2);">
                <button type="button" class="btn btn-muted" data-payment-back>Back</button>
                {{-- Demo-only: simulate provider callbacks. Remove in production. --}}
                <button type="button" class="btn btn-primary" data-payment-simulate="success">Simulate success</button>
                <button type="button" class="btn btn-danger" data-payment-simulate="failed">Simulate failure</button>
            </div>
        </div>
    </div>

    {{-- Stage: result --}}
    <div class="payment-stage" data-payment-stage="status" @if($stage !== 'status') hidden @endif>
        <div class="payment-panel" data-payment-status-host>
            {{ $status ?? '' }}
        </div>
    </div>

    <div class="payment-trust-row" aria-hidden="true">
        <span>256-bit SSL</span>
        <span>PCI partner</span>
        <span>One-time charge</span>
    </div>
</div>
