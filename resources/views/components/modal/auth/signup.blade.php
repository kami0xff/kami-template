{{--
    Guest create-account modal (non-creator) — split form + promo benefits.

    Props: id, brand, freeTokens, tokenLabel
--}}
@props([
    'id' => 'auth-signup',
    'brand' => null,
    'freeTokens' => 50,
    'tokenLabel' => 'JETONS GRATUITS',
    'tokenSub' => 'toutes les heures',
])

@php
    $brand = $brand ?? config('app.name', 'Kami');
@endphp

<x-modal.modal :id="$id" maxWidth="860px" :padded="false" class="auth-guest-overlay auth-guest-overlay--signup">
    <x-slot:head>
        <div class="auth-guest-brand" aria-hidden="true">{{ $brand }}</div>
    </x-slot:head>

    <div class="auth-guest auth-guest--signup" data-auth-guest="signup">
        <div class="auth-guest-col auth-guest-col--form">
            <h2 id="{{ $id }}-title" class="auth-guest-title">Créez un compte gratuitement</h2>

            <form class="auth-guest-form" method="POST" action="{{ Route::has('register') ? route('register') : '#' }}" data-auth-form="signup">
                @csrf

                <label class="auth-guest-field">
                    <span class="visually-hidden">Nom d’utilisateur</span>
                    <input
                        class="auth-guest-input"
                        type="text"
                        name="username"
                        autocomplete="username"
                        placeholder="Nom d’utilisateur"
                        required
                        data-auth-username
                    >
                    <button type="button" class="auth-guest-suggest" data-auth-suggest-username aria-label="Suggérer un nom">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/>
                        </svg>
                    </button>
                </label>

                <button type="submit" class="auth-guest-submit">Créer un compte</button>
            </form>

            <p class="auth-guest-divider"><span>ou continuez avec</span></p>

            <div class="auth-guest-socials">
                <button type="button" class="auth-guest-social" data-auth-social="google" aria-label="Google">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.1A6.6 6.6 0 0 1 5.5 12c0-.73.13-1.44.34-2.1V7.07H2.18A11 11 0 0 0 1 12c0 1.78.42 3.47 1.18 4.97l3.66-2.87z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 0 0 2.18 7.07l3.66 2.84C6.71 7.3 9.14 5.38 12 5.38z"/>
                    </svg>
                </button>
                <button type="button" class="auth-guest-social" data-auth-social="x" aria-label="X">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.727-8.835L1.254 2.25H8.08l4.253 5.622L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>
                    </svg>
                </button>
            </div>

            <button type="button" class="auth-guest-outline" data-auth-continue-guest>
                Continuer sans adresse e-mail
            </button>

            <p class="auth-guest-footer">
                Vous avez déjà un compte ?
                <button type="button" class="auth-guest-footer-link" data-auth-open="login">Connectez-vous</button>
            </p>
        </div>

        <aside class="auth-guest-col auth-guest-col--promo" aria-label="Avantages">
            <div class="auth-guest-reward">
                <div class="auth-guest-reward-num">{{ $freeTokens }}</div>
                <div class="auth-guest-reward-label">{{ $tokenLabel }}</div>
                <div class="auth-guest-reward-sub"><em>{{ $tokenSub }}</em></div>
            </div>

            <ul class="auth-guest-benefits">
                <li>
                    <span class="auth-guest-benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    <span>Chattez avec des <strong>créateurs</strong></span>
                </li>
                <li>
                    <span class="auth-guest-benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v18M8 7h6a3 3 0 0 1 0 6H8"/><circle cx="12" cy="18" r="2"/></svg>
                    </span>
                    <span>Explorez lives, galeries &amp; <strong>manga</strong></span>
                </li>
                <li>
                    <span class="auth-guest-benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
                    </span>
                    <span>Profitez des sessions en <strong>privé</strong></span>
                </li>
                <li>
                    <span class="auth-guest-benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8"/><path d="M12 2v10"/><path d="m8 8 4 4 4-4"/><path d="M2 7h20v5H2z"/></svg>
                    </span>
                    <span>Participez aux <strong>tirages</strong> &amp; bonus</span>
                </li>
                <li>
                    <span class="auth-guest-benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 21 12 17 5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    <span>Ajoutez modèles &amp; contenu en <strong>favoris</strong></span>
                </li>
            </ul>
        </aside>
    </div>

    <div class="auth-guest-legal">
        <p>En créant un compte, vous acceptez nos <a href="#">CGU</a></p>
        <a class="auth-guest-creator-link" href="#" data-auth-creator>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M23 7 16 12l7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/>
            </svg>
            Vous voulez vous inscrire en tant que créateur ?
        </a>
    </div>
</x-modal.modal>
