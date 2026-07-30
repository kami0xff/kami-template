{{--
    Thinking indicator row for AI threads / girlfriend chats.
    Wraps the glowing orb in a soft card bubble.
--}}
@props([
    'mode' => 'thinking',
    'size' => 'sm',
    'label' => null,
    'hint' => null,
    'roleLabel' => null,
])

@php
    $roleLabel = $roleLabel ?? match ($mode) {
        'image' => 'Creating',
        'video' => 'Filming',
        default => 'Thinking',
    };
@endphp

<article
    {{ $attributes->class([
        'ai-msg',
        'ai-msg--assistant',
        'ai-thinking',
        "ai-thinking--{$mode}",
    ]) }}
    data-ai-typing
    data-ai-thinking
    data-mode="{{ $mode }}"
>
    <div class="ai-msg-role">{{ $roleLabel }}</div>
    <div class="ai-thinking-card">
        <x-ai.orb :mode="$mode" :size="$size" :label="$label" :hint="$hint" />
    </div>
</article>
