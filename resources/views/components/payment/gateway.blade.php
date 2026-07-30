{{--
    Third-party gateway host.

    Drop an iframe into $slot, or leave empty for the placeholder.
    Real checkout redirects / embeds the provider; we only style the shell.

    Props:
        provider — display name for the placeholder
        hint     — helper copy
--}}
@props([
    'provider' => 'Payment provider',
    'hint' => 'Complete payment securely with our partner. Tokens are credited after confirmation.',
])

<div {{ $attributes->class(['payment-gateway']) }} data-payment-gateway>
    <div class="payment-gateway-frame" data-payment-gateway-frame>
        @if(trim($slot) !== '')
            {{ $slot }}
        @else
            <div class="payment-gateway-placeholder">
                <span class="payment-gateway-brand">{{ strtoupper($provider) }}</span>
                <strong>Secure checkout</strong>
                <p>{{ $hint }}</p>
                <p style="font-size:var(--text-small); opacity:.75;">
                    Embed provider iframe here, or redirect via <code>checkoutUrl</code>.
                </p>
            </div>
        @endif
    </div>
</div>
