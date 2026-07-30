@props([
    'modelName' => 'meghan_nill',
    'modelTag' => null,
    'rating' => 4.9,
    'ratingCount' => 33,
    'standardPrice' => 12,
    'exclusivePrice' => 32,
    'minDuration' => 10,
    'standardActivities' => 'Talons, Show huileux, Strip-tease, Fétichisme des pieds, Branlette, Ahegao, Fessée, Cameltoe',
    'exclusiveActivities' => 'Doggy style, Fellation, Tittyfuck, Seins nus, Doigtage, Gorge profonde, Footjob',
])

<x-modal.modal id="private-show" maxWidth="880px" :padded="false">
    <x-slot:head>
        <div class="modal-head modal-head--model">
            <div class="modal-head-left">
                {{-- Avatar --}}
                <div class="modal-avatar-wrap">
                    <div class="modal-avatar">
                        <svg viewBox="0 0 80 110" fill="none">
                            <ellipse cx="40" cy="32" rx="16" ry="18" fill="white"/>
                            <path d="M12 110 Q12 72 40 68 Q68 72 68 110Z" fill="white"/>
                        </svg>
                    </div>
                    <div class="modal-avatar-badge" title="{{ __('Privé') }}">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="4" y="11" width="16" height="10" rx="2"/>
                            <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                        </svg>
                    </div>
                </div>

                {{-- Name + tag --}}
                <div class="modal-name-wrap">
                    <div class="modal-model-name">{{ $modelName }}</div>
                    @if($modelTag)
                        <div class="modal-model-tag">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M6 3h12l4 6-10 12L2 9z"/><path d="M11 3 8 9l4 12 4-12-3-6"/><path d="M2 9h20"/>
                            </svg>
                            {{ $modelTag }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Rating --}}
            <div class="modal-rating">
                <div class="modal-rating-count">{{ $ratingCount }} {{ __('évaluations') }}</div>
                <div class="modal-rating-row">
                    <div class="modal-rating-num">{{ $rating }}</div>
                    <div class="modal-stars">
                        @php $fullStars = floor($rating); $hasHalf = ($rating - $fullStars) >= 0.3; @endphp
                        @for($i = 0; $i < $fullStars; $i++)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                        @if($hasHalf)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <defs><linearGradient id="halfstar"><stop offset="50%" stop-color="#c8c8c8"/><stop offset="50%" stop-color="#3a3a3a"/></linearGradient></defs>
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="url(#halfstar)"/>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot:head>

    {{-- BODY: two tier cards --}}
    <div class="modal-body modal-body--grid">

        {{-- STANDARD TIER --}}
        <div class="modal-tier">
            <div class="modal-heart-btn">
                <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.534-9.5-9.5C.6 8 2.7 4 6.5 4c2 0 3.5 1 5.5 3 2-2 3.5-3 5.5-3C21.3 4 23.4 8 21.5 11.5 19 16.466 12 21 12 21z"/></svg>
            </div>
            <div class="modal-tier-title">{{ __('Show privé') }}</div>
            <button class="modal-price-pill">{{ __('Commencez') }} {{ $standardPrice }} jt/min</button>
            <div class="modal-duration">
                {{ __('Durée minimale') }} : <strong>{{ $minDuration }} min</strong>
                <span class="modal-help">?</span>
            </div>

            <div class="modal-features">
                <div class="modal-feature">
                    <span class="modal-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                        </svg>
                    </span>
                    {{ __("Vous recevez l'enregistrement du show") }}
                    <span class="modal-help">?</span>
                </div>
            </div>

            <div class="modal-act-box">
                <div class="modal-act-title">{{ __('Voici les activités proposées') }} :</div>
                <div class="modal-act-list">{{ $standardActivities }}</div>
                <a class="modal-act-more">{{ __('Afficher plus') }}</a>
            </div>
        </div>

        {{-- EXCLUSIVE TIER --}}
        <div class="modal-tier modal-tier--exclusive">
            <div class="modal-heart-btn">
                <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.534-9.5-9.5C.6 8 2.7 4 6.5 4c2 0 3.5 1 5.5 3 2-2 3.5-3 5.5-3C21.3 4 23.4 8 21.5 11.5 19 16.466 12 21 12 21z"/></svg>
            </div>
            <div class="modal-tier-title">{{ __('Show privé exclusif') }}</div>
            <button class="modal-price-pill">{{ __('Commencez') }} {{ $exclusivePrice }} jt/min</button>
            <div class="modal-duration">
                {{ __('Durée minimale') }} : <strong>{{ $minDuration }} min</strong>
                <span class="modal-help">?</span>
            </div>

            <div class="modal-features">
                <div class="modal-feature modal-feature--bold">
                    <span class="modal-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                        </svg>
                    </span>
                    {{ __('Appel vidéo (Cam2Cam) disponible') }}
                    <span class="modal-help">?</span>
                </div>
                <div class="modal-feature">
                    <span class="modal-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </span>
                    {{ __('Personne ne peut espionner le show') }}
                </div>
                <div class="modal-feature">
                    <span class="modal-feature-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                        </svg>
                    </span>
                    {{ __("Vous seul recevez l'enregistrement du show") }}
                    <span class="modal-help">?</span>
                </div>
            </div>

            <div class="modal-act-box">
                <div class="modal-act-title">{{ __('Profitez de toutes les activités proposées en show privé, mais aussi des activités suivantes') }} :</div>
                <div class="modal-act-list">{{ $exclusiveActivities }}</div>
                <a class="modal-act-more">{{ __('Afficher plus') }}</a>
            </div>
        </div>

    </div>

    {{-- Disclaimer --}}
    <div class="modal-disclaimer">
        {{ __("Le modèle ne pourra chatter qu'avec vous et n'aura d'yeux que pour vous.") }}<br>
        {{ __('Personne d\'autre ne peut voir votre discussion. Le show peut être interrompu à tout moment aussi bien par vous que le modèle.') }}
        <div class="modal-lock-divider">
            <div class="modal-green-lock">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="4" y="11" width="16" height="10" rx="2"/>
                    <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="modal-foot">
        <button class="modal-btn-ghost">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <polyline points="20 12 20 22 4 22 4 12"/>
                <rect x="2" y="7" width="20" height="5"/>
                <line x1="12" y1="22" x2="12" y2="7"/>
                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
            </svg>
            {{ __('Offrir un show privé exclusif') }}
        </button>

        <div class="modal-foot-right">
            <div class="modal-btn-split">
                <div class="modal-btn-split-label">
                    {{ __('Show Privé') }}
                    <span class="modal-btn-split-coin">
                        <span class="coin"></span> {{ $standardPrice }} jt
                    </span>
                </div>
                <div class="modal-btn-split-caret">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>
            <button class="modal-btn-primary">{{ __('Démarrer') }}</button>
        </div>
    </div>

</x-modal.modal>
