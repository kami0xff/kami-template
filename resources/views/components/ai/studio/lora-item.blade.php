@props([
    'id' => '',
    'name' => '',
    'image' => null,
    'family' => 'SDXL',
    'weight' => 0.8,
    'min' => -2,
    'max' => 2,
    'step' => 0.05,
])

<div
    class="ai-lora-item"
    data-ai-lora
    data-lora-id="{{ $id }}"
    data-lora-name="{{ $name }}"
    data-lora-family="{{ $family }}"
>
    @if($image)
        <img src="{{ $image }}" alt="">
    @else
        <img src="" alt="" style="background:#222">
    @endif

    <div style="min-width:0;">
        <div class="ai-lora-item-title">{{ $name }}</div>
        <div class="ai-config-chip-sub">{{ $family }}</div>
        <input
            class="ai-range"
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            value="{{ $weight }}"
            data-ai-lora-range
            aria-label="LoRA weight"
        >
    </div>

    <div class="ai-lora-item-controls">
        <input
            class="ai-lora-weight"
            type="number"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            value="{{ $weight }}"
            data-ai-lora-weight
        >
        <button type="button" class="ai-lora-remove" data-ai-lora-remove aria-label="Remove LoRA">×</button>
    </div>
</div>
