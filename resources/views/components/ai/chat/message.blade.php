{{--
    AI chat message (Astryx ChatMessage-inspired).

    Props: role (user|assistant|system), content, images (array of urls)
--}}
@props([
    'role' => 'assistant',
    'content' => '',
    'images' => [],
])

<article {{ $attributes->class(['ai-msg', "ai-msg--{$role}"]) }} data-ai-msg data-role="{{ $role }}">
    <div class="ai-msg-role">
        {{ $role === 'user' ? 'You' : ($role === 'system' ? 'System' : 'Model') }}
    </div>

    @if(is_array($images) && count($images))
        <div class="ai-msg-media">
            @foreach($images as $src)
                <img src="{{ $src }}" alt="" loading="lazy">
            @endforeach
        </div>
    @endif

    @if($content !== '' || trim($slot) !== '')
        <div class="ai-msg-bubble">
            @if($content !== '')
                {{ $content }}
            @else
                {{ $slot }}
            @endif
        </div>
    @endif
</article>
