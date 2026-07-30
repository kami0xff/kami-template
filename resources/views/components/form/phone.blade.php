{{--
    Phone field — country-calling-code prefix + tel input.

    <x-form.phone name="phone" dial="+33" />
    Submits: phone (national number) + phone_cc (dial code, e.g. +33)
--}}
@props([
    'name' => 'phone',
    'value' => '',
    'dial' => '+1',
    'placeholder' => '6 12 34 56 78',
    'disabled' => false,
    'required' => false,
])

@php
    $dials = [
        '+1' => 'US/CA +1',
        '+33' => 'FR +33',
        '+44' => 'UK +44',
        '+49' => 'DE +49',
        '+34' => 'ES +34',
        '+39' => 'IT +39',
        '+380' => 'UA +380',
        '+7' => 'RU +7',
        '+55' => 'BR +55',
        '+81' => 'JP +81',
        '+82' => 'KR +82',
        '+61' => 'AU +61',
        '+31' => 'NL +31',
        '+46' => 'SE +46',
        '+48' => 'PL +48',
        '+90' => 'TR +90',
        '+52' => 'MX +52',
        '+91' => 'IN +91',
    ];
    $selectedDial = old($name . '_cc', $dial);
@endphp

<div {{ $attributes->class(['form-phone']) }} data-phone>
    <label class="form-phone-cc">
        <span class="sr-only">Country code</span>
        <select
            class="form-phone-cc-select"
            name="{{ $name }}_cc"
            @disabled($disabled)
            aria-label="Country calling code"
        >
            @foreach($dials as $code => $label)
                <option value="{{ $code }}" @selected($code === $selectedDial)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <x-form.input
        type="tel"
        :name="$name"
        :value="$value"
        :placeholder="$placeholder"
        :disabled="$disabled"
        :required="$required"
        autocomplete="tel-national"
        inputmode="tel"
    />
</div>
