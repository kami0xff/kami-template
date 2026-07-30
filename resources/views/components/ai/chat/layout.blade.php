{{--
    AI chat layout (Astryx ChatLayout-inspired).

    Slots: sidebar, thread (default), composer, aside
--}}
@props([
    'title' => 'AI Chat',
    'modelLabel' => null,
])

@php
    $hasSidebar = isset($sidebar);
    $hasAside = isset($aside);
    $layoutClass = match (true) {
        $hasSidebar && $hasAside => '',
        $hasSidebar && ! $hasAside => 'ai-chat--no-aside',
        ! $hasSidebar && $hasAside => 'ai-chat--no-sidebar',
        default => 'ai-chat--plain',
    };
@endphp

<div {{ $attributes->class(['ai-chat', $layoutClass]) }} data-ai-chat>
    @isset($sidebar)
        <aside class="ai-chat-sidebar">
            <div class="ai-chat-sidebar-head">
                <h2 class="ai-chat-sidebar-title">Chats</h2>
                <button type="button" class="btn btn-muted btn-xs" data-ai-new-chat>+ New</button>
            </div>
            {{ $sidebar }}
        </aside>
    @endisset

    <section class="ai-chat-main">
        <div class="ai-chat-main-head">
            <h2 class="ai-chat-main-title">{{ $title }}</h2>
            @if($modelLabel)
                <span class="ai-composer-model" data-ai-active-model-label>{{ $modelLabel }}</span>
            @endif
        </div>

        <div class="ai-chat-thread" data-ai-thread>
            {{ $slot }}
        </div>

        @isset($composer)
            {{ $composer }}
        @endisset
    </section>

    @isset($aside)
        <aside class="ai-chat-aside">
            <div class="ai-chat-aside-head">
                <h2 class="ai-chat-aside-title">Active config</h2>
            </div>
            <div class="ai-chat-aside-body">
                {{ $aside }}
            </div>
        </aside>
    @endisset
</div>
