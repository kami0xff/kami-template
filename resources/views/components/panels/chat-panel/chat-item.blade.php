<!-- resources/views/components/panels/chat-item.blade.php -->
@props(['conversation'])

<a href="/messages/{{ $conversation->id }}" class="panel-chat">
    <div class="panel-chat-av-wrap">
        <img src="{{ $conversation->avatar }}" class="panel-av" />
        @if($conversation->isOnline)
            <span class="panel-online-dot"></span>
        @endif
        @if($conversation->isLive)
            <span class="panel-live-badge">LIVE</span>
        @endif
    </div>
    <div class="panel-chat-body">
        <div class="panel-chat-top">
            <strong>{{ $conversation->name }}</strong>
            <span class="panel-meta">{{ $conversation->date }}</span>
        </div>
        <p class="panel-chat-preview">{{ $conversation->lastMessage }}</p>
    </div>
</a>
