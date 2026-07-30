@props([
    'name' => 'password',
    'placeholder' => '',
    'disabled' => false,
    'autofocus' => false,
])

<div class="form-password-wrap">
    <input
        type="password"
        name="{{ $name }}"
        id="{{ $name }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @disabled($disabled)
        @if($autofocus) autofocus @endif
        {{ $attributes->class(['form-input']) }}
    >
    <button type="button" class="form-password-toggle" aria-label="{{ __('Toggle password visibility') }}" onclick="
        const input = this.previousElementSibling;
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        this.querySelector('.eye-open').style.display = isHidden ? 'none' : 'block';
        this.querySelector('.eye-closed').style.display = isHidden ? 'block' : 'none';
    ">
        <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <svg class="eye-closed" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
        </svg>
    </button>
</div>
