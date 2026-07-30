@props([
    'id' => '',
    'title' => 'New chat',
    'meta' => null,
    'active' => false,
])

<li>
    <button
        type="button"
        {{ $attributes->class(['ai-chat-conv', 'is-active' => $active]) }}
        data-ai-conv="{{ $id }}"
    >
        <span class="ai-chat-conv-title">{{ $title }}</span>
        @if($meta)
            <span class="ai-chat-conv-meta">{{ $meta }}</span>
        @endif
    </button>
</li>
