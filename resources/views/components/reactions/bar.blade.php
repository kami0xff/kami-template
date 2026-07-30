{{--
    Reactions bar + Twitch-style emote picker.

    Supports Unicode emoji AND custom image/GIF emotes in named packs.

    Props:
        exclusive — one reaction per user
        items     — active chips on the bar:
                    ['key','count','active', 'emoji'|'src', 'label?', 'animated?']
        sets      — picker packs:
                    [
                      ['id' => 'kami', 'label' => 'Kami', 'emotes' => [
                        ['key' => 'kamiHype', 'label' => 'kamiHype', 'src' => '/emotes/hype.gif', 'animated' => true],
                        ['key' => 'heart', 'emoji' => '❤️'],
                      ]],
                    ]

    Events: reactions:change { key, label, src, emoji, active, reactions }
--}}
@props([
    'exclusive' => false,
    'items' => [],
    'sets' => null,
])

@php
    $defaultUnicode = [
        ['key' => 'heart', 'label' => 'heart', 'emoji' => '❤️'],
        ['key' => 'fire', 'label' => 'fire', 'emoji' => '🔥'],
        ['key' => 'hot', 'label' => 'heart_eyes', 'emoji' => '😍'],
        ['key' => 'laugh', 'label' => 'joy', 'emoji' => '😂'],
        ['key' => 'wow', 'label' => 'open_mouth', 'emoji' => '😮'],
        ['key' => 'clap', 'label' => 'clap', 'emoji' => '👏'],
        ['key' => 'hundred', 'label' => '100', 'emoji' => '💯'],
        ['key' => 'kiss', 'label' => 'kiss', 'emoji' => '😘'],
        ['key' => 'cool', 'label' => 'sunglasses', 'emoji' => '😎'],
        ['key' => 'think', 'label' => 'thinking', 'emoji' => '🤔'],
        ['key' => 'sad', 'label' => 'cry', 'emoji' => '😢'],
        ['key' => 'sparkle', 'label' => 'sparkles', 'emoji' => '✨'],
    ];

    // Demo site pack — swap these URLs for your CDN / storage later.
    $defaultSite = [
        ['key' => 'kamiHype', 'label' => 'kamiHype', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f389.png'],
        ['key' => 'kamiLove', 'label' => 'kamiLove', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f496.png'],
        ['key' => 'kamiFire', 'label' => 'kamiFire', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f525.png'],
        ['key' => 'kamiGG', 'label' => 'kamiGG', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f3ae.png'],
        ['key' => 'kamiLul', 'label' => 'kamiLul', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f602.png'],
        ['key' => 'kamiPog', 'label' => 'kamiPog', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f62e.png'],
        // Animated “fun” reactions (GIF)
        ['key' => 'kamiParty', 'label' => 'kamiParty', 'src' => 'https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'animated' => true],
        ['key' => 'kamiRip', 'label' => 'kamiRip', 'src' => 'https://media.giphy.com/media/3o7TKsQ8XQUy9tYw6Q/giphy.gif', 'animated' => true],
        ['key' => 'kamiHeartGif', 'label' => 'kamiHeartGif', 'src' => 'https://media.giphy.com/media/3oz8xIsloV7x5mm0Ko/giphy.gif', 'animated' => true],
        ['key' => 'kamiWowGif', 'label' => 'kamiWowGif', 'src' => 'https://media.giphy.com/media/3oEjI6S1HKbzk3WZRM/giphy.gif', 'animated' => true],
    ];

    $sets = $sets ?? [
        ['id' => 'site', 'label' => 'Kami', 'emotes' => $defaultSite],
        ['id' => 'global', 'label' => 'Global', 'emotes' => $defaultUnicode],
    ];

    $activeKeys = collect($items)->where('active', true)->pluck('key')->all();
@endphp

<div
    {{ $attributes->class(['reactions']) }}
    data-reactions
    @if($exclusive) data-reactions-exclusive @endif
>
    <div class="reactions-bar" data-reactions-bar>
        @foreach($items as $item)
            <x-reactions.chip
                :name="$item['key']"
                :emoji="$item['emoji'] ?? null"
                :src="$item['src'] ?? null"
                :label="$item['label'] ?? $item['key']"
                :count="$item['count'] ?? 0"
                :active="!empty($item['active'])"
                :animated="!empty($item['animated'])"
            />
        @endforeach

        <button
            type="button"
            class="reaction-add"
            data-reaction-picker-trigger
            aria-label="Add reaction"
            aria-haspopup="true"
            aria-expanded="false"
            title="Add reaction"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path d="M8.5 14.5s1.5 2 3.5 2 3.5-2 3.5-2M9 10h.01M15 10h.01" />
                <path d="M17.5 7.5v-3M16 6h3" />
            </svg>
        </button>
    </div>

    <div class="reaction-picker" data-reaction-picker hidden>
        <div class="reaction-picker-search">
            <input
                type="search"
                class="reaction-picker-input"
                data-reaction-search
                placeholder="Search emotes…"
                autocomplete="off"
                spellcheck="false"
            >
        </div>

        <div class="reaction-picker-tabs" role="tablist" aria-label="Emote packs">
            @foreach($sets as $i => $set)
                <button
                    type="button"
                    class="reaction-picker-tab {{ $i === 0 ? 'is-active' : '' }}"
                    role="tab"
                    data-reaction-tab="{{ $set['id'] }}"
                    aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                >{{ $set['label'] }}</button>
            @endforeach
        </div>

        <div class="reaction-picker-panels">
            @foreach($sets as $i => $set)
                <div
                    class="reaction-picker-panel"
                    data-reaction-panel="{{ $set['id'] }}"
                    role="tabpanel"
                    @if($i !== 0) hidden @endif
                >
                    <div class="reaction-picker-grid" role="listbox" aria-label="{{ $set['label'] }} emotes">
                        @foreach($set['emotes'] as $opt)
                            @php
                                $label = $opt['label'] ?? $opt['key'];
                                $src = $opt['src'] ?? null;
                                $emoji = $opt['emoji'] ?? null;
                                $animated = !empty($opt['animated']) || str_ends_with(strtolower((string) $src), '.gif');
                                $pressed = in_array($opt['key'], $activeKeys, true);
                            @endphp
                            <button
                                type="button"
                                class="reaction-picker-btn"
                                role="option"
                                data-reaction-pick
                                data-reaction="{{ $opt['key'] }}"
                                data-reaction-label="{{ $label }}"
                                @if($emoji) data-reaction-emoji="{{ $emoji }}" @endif
                                @if($src) data-reaction-src="{{ $src }}" @endif
                                @if($animated) data-reaction-animated @endif
                                aria-label=":{{ $label }}:"
                                aria-pressed="{{ $pressed ? 'true' : 'false' }}"
                                title=":{{ $label }}:"
                            >
                                @if($src)
                                    <img
                                        class="reaction-emote-img {{ $animated ? 'reaction-emote-img--animated' : '' }}"
                                        src="{{ $src }}"
                                        alt=""
                                        width="28"
                                        height="28"
                                        loading="lazy"
                                        decoding="async"
                                        draggable="false"
                                    >
                                @else
                                    <span class="reaction-chip-emoji">{{ $emoji }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <p class="reaction-picker-empty" data-reaction-empty hidden>No emotes match</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
