{{--
    Guest login modal (non-creator) — compact Stripchat-style.

    Props: id
--}}
@props([
    'id' => 'auth-login',
])

<x-modal.modal :id="$id" maxWidth="420px" :padded="false" class="auth-guest-overlay auth-guest-overlay--login">
    <x-slot:head>
        <div class="auth-guest-head">
            <h2 id="{{ $id }}-title" class="auth-guest-title">Se connecter</h2>
        </div>
    </x-slot:head>

    <div class="auth-guest auth-guest--login" data-auth-guest="login">
        <form class="auth-guest-form" method="POST" action="{{ Route::has('login') ? route('login') : '#' }}" data-auth-form="login">
            @csrf

            <label class="auth-guest-field">
                <span class="visually-hidden">Nom d’utilisateur ou adresse e-mail</span>
                <input
                    class="auth-guest-input"
                    type="text"
                    name="login"
                    autocomplete="username"
                    placeholder="Nom d’utilisateur ou adresse"
                    required
                >
            </label>

            <label class="auth-guest-field">
                <span class="visually-hidden">Mot de passe</span>
                <input
                    class="auth-guest-input"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    placeholder="Mot de passe"
                    required
                >
            </label>

            <button type="submit" class="auth-guest-submit">Se connecter</button>
        </form>

        <a
            class="auth-guest-link"
            href="{{ Route::has('password.request') ? route('password.request') : '#' }}"
        >Mot de passe oublié ?</a>

        <p class="auth-guest-divider"><span>ou connectez-vous avec</span></p>

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

        <button type="button" class="auth-guest-passless" data-auth-passless>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/>
            </svg>
            Accéder sans mot de passe
        </button>

        <button type="button" class="auth-guest-switch" data-auth-open="signup">
            Créez un compte gratuitement
        </button>
    </div>
</x-modal.modal>
