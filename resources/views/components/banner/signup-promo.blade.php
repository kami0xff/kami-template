{{--
    Sticky bottom signup promo bar (Stripchat-style).

    Props: brand, text, cta, modal (signup modal id)
--}}
@props([
    'brand' => null,
    'text' => null,
    'cta' => "Je m'inscris",
    'modal' => 'auth-signup',
    'dismissible' => true,
])

@php
    $brand = $brand ?? config('app.name', 'Kami');
    $text = $text ?? "Inscrivez-vous à {$brand} pour interagir avec les créateurs !";
@endphp

<div
    {{ $attributes->class(['signup-promo']) }}
    data-signup-promo
    role="region"
    aria-label="Créer un compte"
>
    <div class="signup-promo-inner">
        <span class="signup-promo-mark" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
        </span>
        <p class="signup-promo-text">{{ $text }}</p>
        <button
            type="button"
            class="signup-promo-cta"
            data-auth-open="signup"
            data-auth-modal="{{ $modal }}"
        >{{ $cta }}</button>
    </div>

    @if($dismissible)
        <button type="button" class="signup-promo-close" data-signup-promo-close aria-label="Fermer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    @endif
</div>
