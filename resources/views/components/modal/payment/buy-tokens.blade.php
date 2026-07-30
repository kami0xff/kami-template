<x-modal.modal id="buy-tokens" title="{{ __('Acheter des jetons') }}" :padded="false">
    <x-slot:icon>
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6" />
        </svg>
    </x-slot:icon>

    <form method="POST" action="{{ Route::has('tokens.purchase') ? route('tokens.purchase') : '#' }}" class="modal-form" id="buy-tokens-form">
        @csrf

        <x-tab.tabs id="pm-tabs" class="modal-body modal-body--grid" data-tab-sync="payment_method">


            {{-- LEFT — Payment method tabs --}}
            <div>
                <div class="select-col-label">{{ __('Moyen de paiement') }}</div>

                <x-tab.trigger id="card" :active="true" tag="div" class="select-row">
                    <div class="select-radio"></div>
                    <div class="select-row-main">
                        <div class="select-row-title">{{ __('Carte bancaire') }}</div>
                    </div>
                    <div class="select-row-right">
                        <div class="modal-cb-grid">
                            <div class="modal-cb-brand">
                                <svg width="30" height="10" viewBox="0 0 78 26">
                                    <text x="0" y="20" font-family="Arial Black,Arial" font-weight="900" font-size="22" font-style="italic" fill="#1a1f71">VISA</text>
                                </svg>
                            </div>
                            <div class="modal-cb-brand">
                                <svg width="22" height="14" viewBox="0 0 32 20">
                                    <circle cx="11" cy="10" r="9" fill="#eb001b" />
                                    <circle cx="21" cy="10" r="9" fill="#f79e1b" fill-opacity=".88" />
                                </svg>
                            </div>
                            <div class="modal-cb-brand">
                                <svg width="26" height="14" viewBox="0 0 40 20">
                                    <circle cx="9" cy="10" r="7" fill="#0079be" />
                                    <circle cx="20" cy="10" r="7" fill="#fff" />
                                    <circle cx="31" cy="10" r="7" fill="#ea0029" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </x-tab.trigger>

                <x-tab.trigger id="ideal" tag="div" class="select-row">
                    <div class="select-radio"></div>
                    <div class="select-row-main">
                        <div class="select-row-title">iDEAL | Wero</div>
                    </div>
                    <div class="select-row-right">
                        <div class="modal-pm-logos">
                            <div class="modal-pm-logo modal-pm-logo--ideal">iDEAL</div>
                            <div class="modal-pm-logo modal-pm-logo--wero">WERO</div>
                        </div>
                    </div>
                </x-tab.trigger>

                <x-tab.trigger id="paypal" tag="div" class="select-row">
                    <div class="select-radio"></div>
                    <div class="select-row-main">
                        <div class="select-row-title">PayPal</div>
                    </div>
                    <div class="select-row-right">
                        <div class="modal-pm-logo modal-pm-logo--paypal">
                            <span class="pp1">Pay</span><span class="pp2">Pal</span>
                        </div>
                    </div>
                </x-tab.trigger>

                <x-tab.trigger id="paysafe" tag="div" class="select-row">
                    <div class="select-radio"></div>
                    <div class="select-row-main">
                        <div class="select-row-title">paysafecard</div>
                    </div>
                    <div class="select-row-right">
                        <div class="modal-pm-logo modal-pm-logo--paysafe">paysafe<br>card</div>
                    </div>
                </x-tab.trigger>

                <x-tab.trigger id="skrill" tag="div" class="select-row">
                    <div class="select-radio"></div>
                    <div class="select-row-main">
                        <div class="select-row-title">Skrill</div>
                    </div>
                    <div class="select-row-right">
                        <div class="modal-pm-logo modal-pm-logo--skrill">Skrill</div>
                    </div>
                </x-tab.trigger>

                <x-tab.trigger id="crypto" tag="div" class="select-row">
                    <div class="select-radio"></div>
                    <div class="select-row-main">
                        <div class="select-row-title">USDT, BTC, ETH, USDC</div>
                    </div>
                    <div class="select-row-right">
                        <div class="modal-crypto-icons">
                            <span class="modal-crypto-icon modal-crypto-icon--usdt">₮</span>
                            <span class="modal-crypto-icon modal-crypto-icon--btc">₿</span>
                            <span class="modal-crypto-icon modal-crypto-icon--eth">Ξ</span>
                            <span class="modal-crypto-icon modal-crypto-icon--usdc">$</span>
                        </div>
                    </div>
                </x-tab.trigger>
            </div>

            {{-- RIGHT — Token packs per payment method --}}
            <div>
                <x-tab.panel id="card" :active="true">
                    <div class="select-col-label">{{ __('Packs de jetons') }} — {{ __('Carte bancaire') }}</div>
                    <x-form.radio.group name="amount">
                        @foreach([
                            ['amount' => 10,  'price' => '1,99 €'],
                            ['amount' => 25,  'price' => '3,99 €',  'save' => '0,51 €'],
                            ['amount' => 50,  'price' => '6,99 €',  'save' => '2,01 €'],
                            ['amount' => 100, 'price' => '11,99 €', 'save' => '6,01 €', 'default' => true],
                            ['amount' => 150, 'price' => '16,99 €', 'save' => '10,01 €'],
                            ['amount' => 300, 'price' => '29,99 €', 'save' => '24,01 €'],
                        ] as $pack)
                            <x-form.radio.item
                                value="{{ $pack['amount'] }}"
                                :checked="$pack['default'] ?? false"
                                title="{{ $pack['amount'] }} {{ __('jetons') }}"
                                price="{{ $pack['price'] }}"
                                :save="$pack['save'] ?? null"
                            />
                        @endforeach
                    </x-form.radio.group>
                </x-tab.panel>

                <x-tab.panel id="ideal">
                    <div class="select-col-label">{{ __('Packs de jetons') }} — iDEAL</div>
                    <x-form.radio.group name="amount">
                        @foreach([
                            ['amount' => 10,  'price' => '1,99 €'],
                            ['amount' => 25,  'price' => '3,99 €',  'save' => '0,51 €'],
                            ['amount' => 50,  'price' => '6,99 €',  'save' => '2,01 €', 'default' => true],
                            ['amount' => 100, 'price' => '11,99 €', 'save' => '6,01 €'],
                            ['amount' => 150, 'price' => '16,99 €', 'save' => '10,01 €'],
                            ['amount' => 300, 'price' => '29,99 €', 'save' => '24,01 €'],
                        ] as $pack)
                            <x-form.radio.item
                                value="{{ $pack['amount'] }}"
                                :checked="$pack['default'] ?? false"
                                title="{{ $pack['amount'] }} {{ __('jetons') }}"
                                price="{{ $pack['price'] }}"
                                :save="$pack['save'] ?? null"
                            />
                        @endforeach
                    </x-form.radio.group>
                </x-tab.panel>

                <x-tab.panel id="paypal">
                    <div class="select-col-label">{{ __('Packs de jetons') }} — PayPal</div>
                    <x-form.radio.group name="amount">
                        @foreach([
                            ['amount' => 25,  'price' => '4,49 €'],
                            ['amount' => 50,  'price' => '7,99 €',  'save' => '1,01 €', 'default' => true],
                            ['amount' => 100, 'price' => '13,99 €', 'save' => '4,01 €'],
                            ['amount' => 150, 'price' => '19,99 €', 'save' => '7,01 €'],
                            ['amount' => 300, 'price' => '34,99 €', 'save' => '19,01 €'],
                        ] as $pack)
                            <x-form.radio.item
                                value="{{ $pack['amount'] }}"
                                :checked="$pack['default'] ?? false"
                                title="{{ $pack['amount'] }} {{ __('jetons') }}"
                                price="{{ $pack['price'] }}"
                                :save="$pack['save'] ?? null"
                            />
                        @endforeach
                    </x-form.radio.group>
                </x-tab.panel>

                <x-tab.panel id="paysafe">
                    <div class="select-col-label">{{ __('Packs de jetons') }} — paysafecard</div>
                    <x-form.radio.group name="amount">
                        @foreach([
                            ['amount' => 10,  'price' => '2,49 €'],
                            ['amount' => 25,  'price' => '4,99 €', 'default' => true],
                            ['amount' => 50,  'price' => '9,49 €',  'save' => '0,51 €'],
                            ['amount' => 100, 'price' => '17,99 €', 'save' => '2,01 €'],
                        ] as $pack)
                            <x-form.radio.item
                                value="{{ $pack['amount'] }}"
                                :checked="$pack['default'] ?? false"
                                title="{{ $pack['amount'] }} {{ __('jetons') }}"
                                price="{{ $pack['price'] }}"
                                :save="$pack['save'] ?? null"
                            />
                        @endforeach
                    </x-form.radio.group>
                </x-tab.panel>

                <x-tab.panel id="skrill">
                    <div class="select-col-label">{{ __('Packs de jetons') }} — Skrill</div>
                    <x-form.radio.group name="amount">
                        @foreach([
                            ['amount' => 10,  'price' => '1,99 €'],
                            ['amount' => 25,  'price' => '4,49 €',  'save' => '0,01 €'],
                            ['amount' => 50,  'price' => '7,99 €',  'save' => '1,01 €', 'default' => true],
                            ['amount' => 100, 'price' => '14,99 €', 'save' => '3,01 €'],
                            ['amount' => 150, 'price' => '20,99 €', 'save' => '8,01 €'],
                            ['amount' => 300, 'price' => '37,99 €', 'save' => '16,01 €'],
                        ] as $pack)
                            <x-form.radio.item
                                value="{{ $pack['amount'] }}"
                                :checked="$pack['default'] ?? false"
                                title="{{ $pack['amount'] }} {{ __('jetons') }}"
                                price="{{ $pack['price'] }}"
                                :save="$pack['save'] ?? null"
                            />
                        @endforeach
                    </x-form.radio.group>
                </x-tab.panel>

                <x-tab.panel id="crypto">
                    <div class="select-col-label">{{ __('Packs de jetons') }} — Crypto</div>
                    <x-form.radio.group name="amount">
                        @foreach([
                            ['amount' => 50,  'price' => '5,99 €',  'save' => '3,01 €'],
                            ['amount' => 100, 'price' => '10,99 €', 'save' => '7,01 €', 'default' => true],
                            ['amount' => 150, 'price' => '15,99 €', 'save' => '11,01 €'],
                            ['amount' => 300, 'price' => '27,99 €', 'save' => '26,01 €'],
                        ] as $pack)
                            <x-form.radio.item
                                value="{{ $pack['amount'] }}"
                                :checked="$pack['default'] ?? false"
                                title="{{ $pack['amount'] }} {{ __('jetons') }}"
                                price="{{ $pack['price'] }}"
                                :save="$pack['save'] ?? null"
                            />
                        @endforeach
                    </x-form.radio.group>
                </x-tab.panel>

                {{-- Payment gateway (shared across all methods) --}}
                <div class="modal-section">
                    <div class="modal-section-label">
                        {{ __('Passerelle de paiement') }}
                        <span class="modal-help">?</span>
                    </div>

                    <x-form.radio.group name="gateway">
                        <x-form.radio.item value="paymentico" :checked="true" title="Paymentico" subtitle="{{ __('Facturé par') }} Paymentico">
                            <x-slot:right>
                                <div class="modal-pm-logo modal-pm-logo--gw modal-pm-logo--paymentico">PAYMENTICO</div>
                            </x-slot:right>
                        </x-form.radio.item>

                        <x-form.radio.item value="epoch" title="Epoch">
                            <x-slot:right>
                                <div class="modal-pm-logo modal-pm-logo--gw modal-pm-logo--epoch">epoch</div>
                            </x-slot:right>
                            <x-slot:subtitle>
                                {{ __('Facturé par') }} EPOCH.COM<br>
                                <span style="opacity:.7">*Technius</span>
                            </x-slot:subtitle>
                        </x-form.radio.item>
                    </x-form.radio.group>
                </div>
            </div>

        </x-tab.tabs>

        {{-- CTA --}}
        <div class="modal-cta-wrap">
            <button type="submit" class="modal-cta">{{ __('Continuer') }}</button>
        </div>
    </form>

    {{-- Fine print --}}
    <div class="modal-fine-print">
        {{ __('Prix TTC.') }}<br>
        <svg class="lock" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
        {{ __("Vous ne serez facturé qu'une seule fois, en toute sécurité. Aucuns frais récurrents.") }}
    </div>

    {{-- Trust badges --}}
    <div class="modal-trust-row">
        <div class="modal-trust">
            <span class="modal-trust-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </span>
            <div class="modal-trust-text">
                <strong>COMODO</strong>
                <span>Secure</span>
            </div>
        </div>
        <div class="modal-trust">
            <span class="modal-trust-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="11" width="18" height="11" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </span>
            <div class="modal-trust-text">
                <strong>256 BIT</strong>
                <span>SSL Encryption</span>
            </div>
        </div>
        <div class="modal-trust">
            <span class="modal-trust-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 2 4 6v6c0 5 3.5 9 8 10 4.5-1 8-5 8-10V6l-8-4z" />
                    <circle cx="12" cy="11" r="2.5" />
                </svg>
            </span>
            <div class="modal-trust-text">
                <strong>PRIVACY</strong>
                <span>In Bank Statement</span>
            </div>
        </div>
    </div>

</x-modal.modal>
