{{--
    Amount dock — presets + custom field + buy/tip CTA + balance status.

    <x-tipping.amounts
        :presets="[20,50,100,200,300]"
        :value="20"
        :balance="0"
        cta="Buy tokens"
        action="buy"
    />
--}}
@props([
    'presets' => [20, 50, 100, 200, 300],
    'value' => 20,
    'balance' => null,
    'cta' => 'Send tip',
    'action' => 'tip', // tip | buy
    'customLabel' => 'Custom amount',
    'needMoreLabel' => 'You need more tokens',
    'balanceLabel' => 'remaining',
])

@php
    $current = (int) $value;
    $bal = $balance === null ? null : (int) $balance;
    $needsMore = $bal !== null && $bal < $current;
@endphp

<div {{ $attributes->class(['tipping-amounts-wrap']) }} data-tipping-amounts data-action="{{ $action }}">
    <div class="tipping-amounts" role="group" aria-label="Token amounts">
        @foreach($presets as $n)
            <button
                type="button"
                class="tipping-amount {{ (int) $n === $current ? 'is-active' : '' }}"
                data-tipping-amount="{{ (int) $n }}"
            >{{ (int) $n }}</button>
        @endforeach
    </div>

    <div class="tipping-custom">
        <span class="tipping-custom-label">{{ $customLabel }}</span>
        <input
            class="tipping-custom-input"
            type="number"
            min="1"
            step="1"
            value="{{ $current }}"
            inputmode="numeric"
            data-tipping-custom
            aria-label="{{ $customLabel }}"
        >
    </div>

    <div class="tipping-actions">
        <button type="button" class="tipping-buy" data-tipping-cta>
            {{ $cta }}
        </button>
        <div class="tipping-status" data-tipping-status @if($bal === null) hidden @endif>
            @if($needsMore)
                <span>{{ $needMoreLabel }} — <strong>buy tokens</strong></span>
            @else
                <span>Ready to <strong>{{ $action === 'buy' ? 'buy' : 'tip' }}</strong></span>
            @endif
            <span class="tipping-status-meta" data-tipping-balance>
                {{ $bal === null ? '' : number_format($bal).' '.$balanceLabel }}
            </span>
        </div>
    </div>
</div>
