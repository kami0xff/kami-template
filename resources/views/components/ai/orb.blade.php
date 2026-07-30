{{--
    Glowing thinking / generating orb for AI girlfriend chats.

    Props:
        mode  — thinking | image | video
        size  — sm | md | lg | xl
        label — optional override for the strong title
        hint  — optional second line
        showLabel — show text under/ beside the orb (default true)
--}}
@props([
    'mode' => 'thinking',
    'size' => 'md',
    'label' => null,
    'hint' => null,
    'showLabel' => true,
])

@php
    $copy = [
        'thinking' => ['label' => 'Thinking…', 'hint' => 'She’s composing a reply'],
        'image' => ['label' => 'Creating a photo…', 'hint' => 'Generating an image for you'],
        'video' => ['label' => 'Filming a clip…', 'hint' => 'Rendering a short video'],
    ];
    $defaults = $copy[$mode] ?? $copy['thinking'];
    $label = $label ?? $defaults['label'];
    $hint = $hint ?? $defaults['hint'];
@endphp

<div
    {{ $attributes->class([
        'ai-orb',
        "ai-orb--{$size}",
        "ai-orb--{$mode}",
    ]) }}
    data-ai-orb
    data-mode="{{ $mode }}"
    role="status"
    aria-live="polite"
    aria-label="{{ $label }}"
>
    <div class="ai-orb-stage" aria-hidden="true">
        <span class="ai-orb-glow"></span>
        <span class="ai-orb-ring"></span>
        <span class="ai-orb-sparks"><i></i><i></i><i></i></span>
        <span class="ai-orb-core"></span>
    </div>

    @if($showLabel)
        <div class="ai-orb-label">
            <strong>{{ $label }}</strong>
            @if($hint)
                <span>{{ $hint }}</span>
            @endif
        </div>
    @endif
</div>
