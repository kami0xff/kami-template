{{-- Auth Modal — Login / Signup --}}
<x-modal.modal id="auth" maxWidth="940px" :padded="false">
    <x-slot:head>
        {{-- Hero replaces the standard modal header --}}
        <div class="auth-hero">
            <div class="auth-hero-bg"></div>

            <div class="auth-hero-top">
                <div class="auth-hero-logo">{{ config('app.name', 'BOYCAM') }}</div>
                <div class="auth-hero-eyebrow">{{ __('auth.hero_eyebrow', ['default' => 'Live · VR · Private']) }}</div>
            </div>

            <div class="auth-hero-bottom">
                <h1>{{ __('auth.hero_title_line1', ['default' => 'WELCOME']) }}<br><em>{{ __('auth.hero_title_line2', ['default' => 'BACK']) }}</em>
                </h1>
                <p>{{ __('auth.hero_description', ['default' => 'Une plateforme unifiée pour le streaming, la VR immersive, le chat en temps réel et la monétisation directe.']) }}
                </p>

                <div class="auth-hero-stats">
                    <div>
                        <div class="auth-hero-stat-num"><span class="auth-live-dot"></span>8,360</div>
                        <div class="auth-hero-stat-label">Live Now</div>
                    </div>
                    <div>
                        <div class="auth-hero-stat-num">4K</div>
                        <div class="auth-hero-stat-label">Stream</div>
                    </div>
                    <div>
                        <div class="auth-hero-stat-num">360°</div>
                        <div class="auth-hero-stat-label">VR Ready</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:head>

    {{-- Form panel — tab-root so TabManager picks it up --}}
    <div class="auth-form-panel tab-root" id="auth-tabs">

        {{-- Pill tabs --}}
        <div class="auth-tabs" role="tablist">
            <x-tab.trigger name="login" :active="true" class="auth-tab">
                {{ __('auth.login') }}
            </x-tab.trigger>
            <x-tab.trigger name="signup" class="auth-tab">
                {{ __('auth.register') }}
            </x-tab.trigger>
        </div>

        {{-- ═══ LOGIN VIEW ═══ --}}
        <x-tab.panel name="login" :active="true" class="auth-view">
            <div class="auth-title">{{ __('auth.login_title', ['default' => 'Heureux de vous revoir']) }}</div>
            <div class="auth-subtitle">
                {{ __('auth.login_subtitle', ['default' => 'Connectez-vous pour reprendre où vous vous êtes arrêté.']) }}
            </div>

            {{-- Social login --}}
            <div class="auth-socials">
                <button type="button" class="auth-social-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.1A6.6 6.6 0 0 1 5.5 12c0-.73.13-1.44.34-2.1V7.07H2.18A11 11 0 0 0 1 12c0 1.78.42 3.47 1.18 4.97l3.66-2.87z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 0 0 2.18 7.07l3.66 2.84C6.71 7.3 9.14 5.38 12 5.38z" />
                    </svg>
                    Google
                </button>
                <button type="button" class="auth-social-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M17.05 12.04c-.03-3 2.45-4.45 2.56-4.52-1.4-2.04-3.57-2.32-4.34-2.35-1.85-.19-3.61 1.09-4.55 1.09s-2.39-1.07-3.93-1.04C4.84 5.25 3 6.46 2 8.3c-1.97 3.41-.5 8.45 1.42 11.23.94 1.36 2.05 2.88 3.51 2.83 1.42-.06 1.95-.92 3.66-.92 1.71 0 2.19.92 3.69.89 1.53-.03 2.49-1.38 3.42-2.74 1.08-1.57 1.52-3.1 1.55-3.18-.03-.01-2.97-1.14-3-4.37zM14.32 3.18c.78-.95 1.31-2.27 1.16-3.58-1.13.05-2.49.75-3.3 1.7-.72.83-1.36 2.18-1.19 3.47 1.26.1 2.55-.64 3.33-1.59z" />
                    </svg>
                    Apple
                </button>
                <button type="button" class="auth-social-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.6-1.4-1.4-1.8-1.4-1.8-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.8-1.6-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2-.2-.3-.6-1.5.1-3.2 0 0 1-.3 3.3 1.2a11.4 11.4 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.7 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.7-5.5 6 .4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3" />
                    </svg>
                    GitHub
                </button>
            </div>

            <div class="auth-divider">
                <div class="auth-divider-line"></div>
                <span class="auth-divider-text">{{ __('auth.or_email', ['default' => 'Ou avec votre email']) }}</span>
                <div class="auth-divider-line"></div>
            </div>

            <x-form.form method="POST" action="{{ Route::has('login') ? route('login') : '#' }}">
                <div class="auth-field">
                    <div class="auth-field-label">{{ __('auth.email') }}</div>
                    <div class="auth-field-wrap">
                        <span class="auth-field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </span>
                        <x-form.input type="email" name="email" placeholder="nom@exemple.com" />
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-field-label">
                        {{ __('auth.password') }}
                        <a
                            href="{{ Route::has('password.request') ? route('password.request') : '#' }}">{{ __('auth.forgot_password', ['default' => 'Mot de passe oublié ?']) }}</a>
                    </div>
                    <div class="auth-field-wrap">
                        <span class="auth-field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <x-form.password name="password" placeholder="••••••••••••" />
                    </div>
                </div>

                <x-atom.checkbox.checkbox name="remember"
                    label="{{ __('auth.remember_me', ['default' => 'Rester connecté pendant 30 jours']) }}" />

                <button type="submit" class="auth-cta" style="margin-top: 18px">
                    {{ __('auth.login_cta', ['default' => 'Se connecter']) }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
            </x-form.form>

            <div class="auth-foot">
                {{ __('auth.no_account', ['default' => 'Pas encore de compte ?']) }}<a href="#"
                    data-auth-switch="signup">{{ __('auth.create_account', ['default' => 'Créer un compte gratuit']) }}</a>
            </div>

            <div class="auth-trust">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                {{ __('auth.trust_login', ['default' => '256-bit SSL · Confidentialité garantie']) }}
            </div>
        </x-tab.panel>

        {{-- ═══ SIGNUP VIEW ═══ --}}
        <x-tab.panel name="signup" class="auth-view">
            <div class="auth-title">{{ __('auth.signup_title', ['default' => 'Créez votre compte']) }}</div>
            <div class="auth-subtitle">
                {{ __('auth.signup_subtitle', ['default' => 'Commencez gratuitement — aucun moyen de paiement requis.']) }}
            </div>

            {{-- Social signup --}}
            <div class="auth-socials">
                <button type="button" class="auth-social-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.1A6.6 6.6 0 0 1 5.5 12c0-.73.13-1.44.34-2.1V7.07H2.18A11 11 0 0 0 1 12c0 1.78.42 3.47 1.18 4.97l3.66-2.87z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 0 0 2.18 7.07l3.66 2.84C6.71 7.3 9.14 5.38 12 5.38z" />
                    </svg>
                    Google
                </button>
                <button type="button" class="auth-social-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M17.05 12.04c-.03-3 2.45-4.45 2.56-4.52-1.4-2.04-3.57-2.32-4.34-2.35-1.85-.19-3.61 1.09-4.55 1.09s-2.39-1.07-3.93-1.04C4.84 5.25 3 6.46 2 8.3c-1.97 3.41-.5 8.45 1.42 11.23.94 1.36 2.05 2.88 3.51 2.83 1.42-.06 1.95-.92 3.66-.92 1.71 0 2.19.92 3.69.89 1.53-.03 2.49-1.38 3.42-2.74 1.08-1.57 1.52-3.1 1.55-3.18-.03-.01-2.97-1.14-3-4.37zM14.32 3.18c.78-.95 1.31-2.27 1.16-3.58-1.13.05-2.49.75-3.3 1.7-.72.83-1.36 2.18-1.19 3.47 1.26.1 2.55-.64 3.33-1.59z" />
                    </svg>
                    Apple
                </button>
                <button type="button" class="auth-social-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.6-1.4-1.4-1.8-1.4-1.8-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.8-1.6-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2-.2-.3-.6-1.5.1-3.2 0 0 1-.3 3.3 1.2a11.4 11.4 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.7 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.7-5.5 6 .4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3" />
                    </svg>
                    GitHub
                </button>
            </div>

            <div class="auth-divider">
                <div class="auth-divider-line"></div>
                <span
                    class="auth-divider-text">{{ __('auth.or_email_signup', ['default' => 'Ou inscription par email']) }}</span>
                <div class="auth-divider-line"></div>
            </div>

            <x-form.form method="POST" action="{{ Route::has('register') ? route('register') : '#' }}">
                <div class="auth-field">
                    <div class="auth-field-label">{{ __('auth.username', ['default' => "Nom d'utilisateur"]) }}</div>
                    <div class="auth-field-wrap">
                        <span class="auth-field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <x-form.input name="name" placeholder="@votre_pseudo" />
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-field-label">{{ __('auth.email') }}</div>
                    <div class="auth-field-wrap">
                        <span class="auth-field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </span>
                        <x-form.input type="email" name="email" placeholder="nom@exemple.com" />
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-field-label">{{ __('auth.password') }}</div>
                    <div class="auth-field-wrap">
                        <span class="auth-field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <x-form.password name="password"
                            placeholder="{{ __('auth.password_min', ['default' => 'Min. 8 caractères']) }}" />
                    </div>
                </div>

                <x-atom.checkbox.checkbox name="age_terms" :checked="true"
                    label="{{ __('auth.age_terms', ['default' => "J'ai 18 ans ou plus et j'accepte les conditions & la politique de confidentialité"]) }}" />

                <button type="submit" class="auth-cta" style="margin-top: 18px">
                    {{ __('auth.signup_cta', ['default' => 'Créer mon compte']) }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </button>
            </x-form.form>

            <div class="auth-foot">
                {{ __('auth.has_account', ['default' => 'Vous avez déjà un compte ?']) }}<a href="#"
                    data-auth-switch="login">{{ __('auth.sign_in', ['default' => 'Se connecter']) }}</a>
            </div>

            <div class="auth-trust">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                {{ __('auth.trust_signup', ['default' => 'Sans frais cachés · Annulation à tout moment']) }}
            </div>
        </x-tab.panel>

    </div>
</x-modal.modal>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('auth-tabs');
        if (!root) return;

        root.querySelectorAll('[data-auth-switch]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                var trigger = root.querySelector('[data-tab-trigger-id="' + a.dataset.authSwitch + '"]');
                if (trigger) trigger.click();
            });
        });
    });
</script>
