{{--
    Token amount picker — chips + optional stepper.

    <x-form.token-picker
        name="tokens"
        :options="[
            ['value' => 100, 'label' => '100'],
            ['value' => 500, 'label' => '500', 'bonus' => '+5%', 'selected' => true],
            ['value' => 1000, 'label' => '1 000', 'bonus' => '+10%'],
        ]"
        :stepper="true"
    />
--}}
@props([
    'name' => 'tokens',
    'options' => [],
    'stepper' => false,
    'min' => 10,
    'max' => 10000,
    'step' => 10,
])

@php
    $selected = collect($options)->firstWhere('selected', true)['value']
        ?? (collect($options)->first()['value'] ?? $min);
@endphp

<div
    {{ $attributes->class(['form-token-picker']) }}
    data-form-token-picker
    data-name="{{ $name }}"
    data-min="{{ $min }}"
    data-max="{{ $max }}"
    data-step="{{ $step }}"
>
    <div class="form-token-chips" data-token-chips>
        @foreach($options as $opt)
            @php
                $val = $opt['value'] ?? 0;
                $label = $opt['label'] ?? $val;
                $bonus = $opt['bonus'] ?? null;
                $isSelected = !empty($opt['selected']) || (int) $val === (int) $selected;
            @endphp
            <button
                type="button"
                @class(['form-token-chip', 'selected' => $isSelected])
                data-token-chip
                data-value="{{ $val }}"
            >
                @if($bonus)<span class="form-token-bonus">{{ $bonus }}</span>@endif
                <strong>{{ $label }}</strong>
                <small>tokens</small>
            </button>
        @endforeach
    </div>

    @if($stepper)
        <div class="form-stepper" data-token-stepper>
            <button type="button" class="form-stepper-btn" data-token-dec aria-label="Decrease">−</button>
            <input type="number" class="form-stepper-input" data-token-input value="{{ $selected }}" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}">
            <button type="button" class="form-stepper-btn" data-token-inc aria-label="Increase">+</button>
        </div>
    @endif

    <input type="hidden" name="{{ $name }}" value="{{ $selected }}" data-token-hidden>
</div>
