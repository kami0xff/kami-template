@props([
    'brand' => null,        // brand text, defaults to app name
    'brandHref' => null,    // brand link target, defaults to home route
    'search' => true,       // show the center search bar (when no $center slot given)
    'hamburger' => true,    // show the sidebar hamburger toggle
])

@php
    $brand = $brand ?? config('app.name', 'App');
    $brandHref = $brandHref ?? localized_route('home');
@endphp

<nav class="navbar" id="navbar">
    <div class="nav-left">
        @if($hamburger)
            <button class="nav-hamburger" type="button" title="Collapse sidebar" aria-label="Collapse sidebar" aria-expanded="true" aria-controls="sidebar" onclick="toggleAppSidebar()">
                <div class="hb-line"></div>
                <div class="hb-line"></div>
                <div class="hb-line"></div>
            </button>
        @endif

        <a class="nav-logo" href="{{ $brandHref }}">{{ $brand }}</a>

        {{ $left ?? '' }}
    </div>

    <div class="nav-center" id="nav-search-layer" data-nav-search>
        @isset($center)
            {{ $center }}
        @elseif($search)
            <x-search />
        @endisset

        @if($search || isset($center))
            <button
                type="button"
                class="nav-search-close"
                data-subnav-search-close
                aria-label="Close search"
                title="Close search"
                hidden
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        @endif
    </div>

    <div class="nav-right">
        @isset($actions)
            {{ $actions }}
        @else
            {{-- Mobile fallback when there is no subnavbar --}}
            @if($search)
                <button
                    type="button"
                    class="nav-icon-btn nav-search-toggle"
                    data-subnav-search-toggle
                    aria-label="Open search"
                    aria-expanded="false"
                    aria-controls="nav-search-layer"
                    title="Search"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                </button>
            @endif

            {{-- Default actions: promo (float drawer) + notifications + account --}}
            <button
                class="nav-icon-btn nav-icon-btn--promo"
                type="button"
                title="Promotions"
                aria-label="Promotions"
                data-action="drawer"
                aria-controls="promo-drawer"
                aria-expanded="false"
                onclick="panels.close('user-panel'); drawers.close('notifications-drawer'); drawers.toggle('promo-drawer')"
            >
                <x-icons.promo />
                <span class="nav-icon-notification-dot" aria-hidden="true"></span>
            </button>

            <button
                class="nav-icon-btn"
                type="button"
                title="Notifications"
                aria-label="Notifications"
                data-action="drawer"
                aria-controls="notifications-drawer"
                aria-expanded="false"
                onclick="panels.close('user-panel'); drawers.close('promo-drawer'); drawers.toggle('notifications-drawer')"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                <div class="nav-icon-notification-dot"></div>
            </button>

            <x-user-avatar
                size="sm"
                :interactive="true"
                :notification="true"
                data-action="panel"
                data-panel-trigger
                aria-controls="user-panel"
                aria-expanded="false"
                aria-haspopup="menu"
                onclick="drawers.close('promo-drawer'); drawers.close('notifications-drawer'); panels.toggle('user-panel')"
            />

            <x-drawer
                id="promo-drawer"
                side="right"
                variant="float"
                title="Promotions"
                class="drawer--promo"
            >
                <div class="drawer-promo">
                    <div class="drawer-promo-banner">
                        <div class="drawer-promo-banner-icon" aria-hidden="true">
                            <x-icons.promo width="22" height="22" />
                        </div>
                        <div class="drawer-promo-banner-copy">
                            <strong>Get the app</strong>
                            <span>Faster access, push alerts, and exclusive drops.</span>
                        </div>
                        <button type="button" class="drawer-promo-banner-dismiss" aria-label="Dismiss" onclick="this.closest('.drawer-promo-banner')?.remove()">
                            &times;
                        </button>
                        <button type="button" class="drawer-promo-qr">
                            Show QR code
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                    </div>

                    <div class="drawer-promo-list" role="list">
                        @foreach([
                            ['title' => 'Weekend tokens', 'text' => '20% bonus on your next top-up', 'tag' => 'New'],
                            ['title' => 'Invite a friend', 'text' => 'Earn credits when they join', 'tag' => null],
                            ['title' => 'Creator spotlight', 'text' => 'Free tips on featured lives tonight', 'tag' => 'Live'],
                        ] as $offer)
                            <a href="#" class="drawer-promo-item" role="listitem">
                                <span class="drawer-promo-item-body">
                                    <span class="drawer-promo-item-title">
                                        {{ $offer['title'] }}
                                        @if($offer['tag'])
                                            <span class="drawer-promo-tag">{{ $offer['tag'] }}</span>
                                        @endif
                                    </span>
                                    <span class="drawer-promo-item-text">{{ $offer['text'] }}</span>
                                </span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </a>
                        @endforeach
                    </div>

                    <button type="button" class="drawer-promo-foot" onclick="drawers.close('promo-drawer')">
                        Maybe later
                    </button>
                </div>
            </x-drawer>

            <x-drawer id="notifications-drawer" side="right" title="Notifications" class="drawer--notifications">
                <div class="panel-feed" role="list">
                    @foreach([
                        ['img' => 12, 'name' => 'MilaSunny', 'text' => 'started a live show', 'time' => '2 min ago', 'unread' => true],
                        ['img' => 32, 'name' => 'KiraNoir', 'text' => 'sent you a message', 'time' => '1 h ago', 'unread' => true],
                        ['img' => 45, 'name' => 'AlexBlaze', 'text' => 'accepted your friend request', 'time' => 'yesterday', 'unread' => false],
                    ] as $n)
                        <a href="#" class="panel-feed-item {{ $n['unread'] ? 'is-unread' : '' }}" role="listitem">
                            <x-user-avatar size="sm" url="https://i.pravatar.cc/64?img={{ $n['img'] }}" :name="$n['name']" />
                            <span class="panel-feed-body">
                                <span class="panel-feed-text"><strong>{{ $n['name'] }}</strong> {{ $n['text'] }}</span>
                                <span class="panel-feed-meta">{{ $n['time'] }}</span>
                            </span>
                            @if($n['unread'])
                                <span class="panel-feed-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
                <x-panels.panel-divider />
                <x-panels.panel-list-item icon="bell" href="#">Mentions</x-panels.panel-list-item>
                <x-panels.panel-list-item icon="message" href="#">Messages</x-panels.panel-list-item>
                <x-panels.panel-list-item icon="heart" href="#">Activity</x-panels.panel-list-item>
            </x-drawer>

            <x-panels.panel id="user-panel" width="280px">
                <x-panels.user-panel.user-panel-head
                    username="Kami Dev"
                    handle="@kami"
                    avatar="https://i.pravatar.cc/96?img=12"
                />
                <x-panels.panel-divider />
                <x-panels.panel-list-item icon="user" href="#">Profile</x-panels.panel-list-item>
                <x-panels.panel-list-item icon="folder" href="#">Settings</x-panels.panel-list-item>
                <x-panels.panel-divider />
                <x-panels.panel-list-item icon="user" href="#">Sign out</x-panels.panel-list-item>
            </x-panels.panel>
        @endisset
    </div>
</nav>
