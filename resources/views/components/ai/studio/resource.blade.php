{{--
    Model / LoRA / embedding card (CivitAI-style).
--}}
@props([
    'id' => '',
    'name' => '',
    'type' => 'checkpoint', // checkpoint | lora | embedding | vae
    'version' => null,
    'base' => null,
    'image' => null,
    'selected' => false,
])

@php
    $family = match (true) {
        str_contains((string) $base, '1.5') => 'SD 1.5',
        str_contains((string) $base, 'Pony') => 'Pony',
        str_contains((string) $base, 'Flux') => 'Flux',
        str_contains((string) $base, 'Illustrious') => 'Illustrious',
        default => str_contains((string) $base, 'SDXL') || str_contains((string) $base, 'XL') ? 'SDXL' : (string) $base,
    };
@endphp

<button
    type="button"
    {{ $attributes->class(['ai-resource', 'is-selected' => $selected]) }}
    data-ai-resource
    data-resource-id="{{ $id }}"
    data-resource-type="{{ $type }}"
    data-resource-name="{{ $name }}"
    data-resource-version="{{ $version }}"
    data-resource-base="{{ $base }}"
    data-resource-family="{{ $family }}"
    data-resource-image="{{ $image }}"
>
    <span class="ai-resource-badge ai-resource-badge--{{ $type }}">{{ $type }}</span>
    <span class="ai-resource-thumb">
        @if($image)
            <img src="{{ $image }}" alt="" loading="lazy">
        @endif
    </span>
    <span class="ai-resource-meta">
        <span class="ai-resource-title">{{ $name }}</span>
        <span class="ai-resource-sub">
            {{ collect([$version, $base])->filter()->implode(' · ') }}
        </span>
    </span>
</button>
