{{--
    Chat dock — single bottom stack for people + AI model chats.

    Mount once per page (app-shell does this). Drive it with:

        chats.open({ id, name, avatar, status, messages })
        chats.openAi(configId | config)   // AI models / girlfriends
        chats.setThinking(id, 'image')
        chats.minimize(id) / chats.expand(id) / chats.close(id)

    window.aiDock is a thin alias of the same dock.

    <x-chat.dock />
--}}
<div
    {{ $attributes->class(['chat-dock']) }}
    id="chat-dock"
    data-chat-dock
    aria-live="polite"
>
    <div class="chat-dock-windows" data-chat-windows></div>
    <div class="chat-dock-heads" data-chat-heads>
        <button
            type="button"
            class="chat-pill chat-pill--more"
            data-chat-heads-more
            aria-label="More chats"
            aria-haspopup="menu"
            aria-expanded="false"
            hidden
        >
            <span class="chat-pill-more-label" aria-hidden="true">…</span>
            <span class="chat-pill-badge" data-chat-heads-more-badge hidden>0</span>
        </button>
        <div class="chat-heads-menu" data-chat-heads-menu hidden role="menu" aria-label="All chats"></div>
    </div>
</div>
