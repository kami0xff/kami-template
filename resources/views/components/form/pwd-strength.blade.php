{{--
    Password strength meter (+ optional checklist).

    Place next to <x-form.password>. Bind with data-for="{input name}" or wrap nearby.

    <x-form.pwd-strength for="password" :level="2" :checklist="true" />
--}}
@props([
    'for' => '',
    'level' => 0,
    'checklist' => true,
    'label' => null,
])

@php
    $labels = [0 => 'Too weak', 1 => 'Weak', 2 => 'Fair', 3 => 'Good', 4 => 'Strong'];
    $level = max(0, min(4, (int) $level));
@endphp

<div
    {{ $attributes->class(['form-pwd-strength']) }}
    data-form-pwd-strength
    data-level="{{ $level }}"
    @if($for) data-for="{{ $for }}" @endif
>
    <div class="form-pwd-bar" aria-hidden="true">
        <span class="form-pwd-seg"></span>
        <span class="form-pwd-seg"></span>
        <span class="form-pwd-seg"></span>
        <span class="form-pwd-seg"></span>
    </div>
    <div class="form-pwd-label">
        <span>Strength</span>
        <strong data-pwd-label>{{ $label ?? $labels[$level] }}</strong>
    </div>
    @if($checklist)
        <div class="form-pwd-checklist" data-pwd-checklist>
            <span class="form-pwd-check" data-rule="length"><span class="ic">✓</span> 8+ chars</span>
            <span class="form-pwd-check" data-rule="upper"><span class="ic">✓</span> Uppercase</span>
            <span class="form-pwd-check" data-rule="number"><span class="ic">✓</span> Number</span>
            <span class="form-pwd-check" data-rule="symbol"><span class="ic">✓</span> Symbol</span>
        </div>
    @endif
</div>
