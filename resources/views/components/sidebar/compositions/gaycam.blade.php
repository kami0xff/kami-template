@php

    $platformData = [
        'stripchat' => ['name' => 'Stripchat', 'color' => '#903232', 'logo' => 'stripchat-logo.svg'],
        'chaturbate' => ['name' => 'Chaturbate', 'color' => '#F57C00', 'logo' => 'chaturbate-logo.svg'],
        'bongacams' => ['name' => 'BongaCams', 'color' => '#E91E63', 'logo' => 'bongacams-logo.svg'],
        'xlovecam' => ['name' => 'XloveCam', 'color' => '#9C27B0', 'logo' => 'xlovecam-logo.svg'],
    ];

    $policyLinks = [
        ['label' => 'About GAY.CAM', 'href' => url('/about')],
        ['label' => 'Blog', 'href' => url('/blog')],
        ['label' => 'Support', 'href' => url('/contact')],
        ['label' => 'Report Content', 'href' => url('/report')],
        ['label' => 'Privacy Policy', 'href' => url('/privacy'), 'rel' => 'nofollow'],
        ['label' => 'Terms of Use', 'href' => url('/terms'), 'rel' => 'nofollow'],
        ['label' => '18 U.S.C. 2257', 'href' => url('/2257'), 'rel' => 'nofollow'],
    ];
@endphp

<!-- DATA in the above block should be provided by the controller  -->
<!-- TODO one feature i would like to implement is that users can pin categories to their sidebar but this requires some DB -->
<!-- want to implement this feature without AI create the model the table and controller logic when rendering the sidebar passing the table 
  $pined categoies and $pined streamer or favorite streamears online also  -->
<!--Optionaly pass in top streamers also and render like twitch on the side navigation  -->

<x-sidebar.sidebar>
    <div class="sidebar-fade">
        <div class="sidebar-banners">
            <!-- <x-sidebar.banner href="{{ url('/subscriptions') }}">
                Get extra benefits
            </x-sidebar.banner> -->

            <!-- can imagine 4 types of cards 2 col 1 col icon text text icon 
             and just an image then specific styling and colors for each cases
             
             pills cards also but maybe not specific to a card
             so lets setup this system properly with our brains then check with ai
            
            performer preview card like twitch or kick 
             -->

            <x-sidebar.card>
                <x-slot:media>
                    <x-atom.button.liquid-metal href="{{ localized_route('roulette') }}" variant="border"
                        aria-label="{{ __('common.roulette') }}">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </x-atom.button.liquid-metal>
                </x-slot:media>

                <a href="{{ localized_route('roulette') }}" class="sidebar-card-copy">
                    <span class="sidebar-card-kicker">{{ __('common.roulette') }}</span>
                    <span class="sidebar-card-text">Start a random live cam</span>
                </a>
            </x-sidebar.card>

            <x-sidebar.card>
                <x-slot:media>
                    <img src="{{ asset("img/joystick.png") }}" width="100px">
                </x-slot:media>
            </x-sidebar.card>

            <x-sidebar.banner>
                <!-- <x-slot:media>
                    <img src="{{ asset("img/joystick.png") }}" width="100px">
                </x-slot:media> -->
            </x-sidebar.banner>

            {{-- === COMPLEX DROPDOWN: covers nesting, actions, all item types === --}}
            <x-sidebar.dropdown id="categories">
                <x-slot:trigger>
                    <x-sidebar.dropdown-trigger icon="tag">
                        Categories
                    </x-sidebar.dropdown-trigger>
                </x-slot:trigger>

                {{-- Flat group: simple link items --}}
                <x-sidebar.dropdown-group title="Browse">
                    <x-sidebar.dropdown-item :href="'niche.show'" :params="'men'">Men</x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item :href="'niche.show'" :params="'couples'">Couples</x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item :href="'niche.show'" :params="'trans'">Trans</x-sidebar.dropdown-item>
                </x-sidebar.dropdown-group>

                {{-- Level 2: filesystem-style nested dropdown --}}
                <x-sidebar.dropdown id="cat-tags">
                    <x-slot:trigger>
                        <x-sidebar.dropdown-trigger icon="bookmark">
                            Popular Tags
                        </x-sidebar.dropdown-trigger>
                    </x-slot:trigger>

                    <x-sidebar.dropdown-item :href="'tags.show'" :params="'asian'">Asian</x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item :href="'tags.show'" :params="'twink'">Twink</x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item :href="'tags.show'" :params="'muscle'">Muscle</x-sidebar.dropdown-item>

                    {{-- Level 3: deeper nesting --}}
                    <x-sidebar.dropdown id="cat-tags-body">
                        <x-slot:trigger>
                            <x-sidebar.dropdown-trigger>
                                Body Type
                            </x-sidebar.dropdown-trigger>
                        </x-slot:trigger>

                        <x-sidebar.dropdown-item :href="'tags.show'" :params="'athletic'">Athletic</x-sidebar.dropdown-item>
                        <x-sidebar.dropdown-item :href="'tags.show'" :params="'slim'">Slim</x-sidebar.dropdown-item>
                        <x-sidebar.dropdown-item :href="'tags.show'" :params="'bear'">Bear</x-sidebar.dropdown-item>
                    </x-sidebar.dropdown>
                </x-sidebar.dropdown>

                {{-- Level 2: another sibling nested dropdown (tests sibling closing) --}}
                <x-sidebar.dropdown id="cat-regions">
                    <x-slot:trigger>
                        <x-sidebar.dropdown-trigger icon="globe">
                            Regions
                        </x-sidebar.dropdown-trigger>
                    </x-slot:trigger>

                    <x-sidebar.dropdown-item :href="'tags.show'" :params="'european'">Europe</x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item :href="'tags.show'" :params="'latin'">Latin America</x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item :href="'tags.show'" :params="'asian'">Asia</x-sidebar.dropdown-item>
                </x-sidebar.dropdown>

                {{-- Action items: panel, modal, sub-sidebar --}}
                <x-sidebar.dropdown-group title="Actions">
                    <x-sidebar.dropdown-item action="panel" target="filter-panel">
                        Open Filters Panel
                    </x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item action="modal" target="tag-settings">
                        Tag Settings
                    </x-sidebar.dropdown-item>
                    <x-sidebar.dropdown-item action="sub-sidebar" target="all-tags">
                        Browse All Tags
                    </x-sidebar.dropdown-item>
                </x-sidebar.dropdown-group>
            </x-sidebar.dropdown>

            <x-sidebar.stream-card username="pipotest" viewer="1000">
                <!-- then we also need to pass data on the streamers we want to display to the sidebar
                    favorites of the user / recommendations 
                -->
                <!-- can we pass json as props i don't remember that might be easier  -->
                <!-- comment passer la data ici props ou compo je pense props vus que il y auras que 1 styleki  -->
            </x-sidebar.stream-card>
        </div>

        <nav class="sidebar-main-links" aria-label="{{ __('common.browse') }}">
            <x-sidebar.link :href="localized_route('home')" icon="home" variant="main"
                :active="request()->routeIs('home*')">
                {{ __('common.home') }}
            </x-sidebar.link>

            <x-sidebar.link :href="localized_route('explore')" icon="feed" variant="main">
                {{ __('common.Feed') }}
            </x-sidebar.link>

            <x-sidebar.link :href="localized_route('explore')" icon="feed" variant="main">
                {{ __('common.explore') }}
            </x-sidebar.link>

            <x-sidebar.link :href="localized_route('niche.show', 'men')" icon="star" variant="main">
                {{ __('common.favorites') }}
            </x-sidebar.link>

            <x-sidebar.link :href="localized_route('roulette')" icon="heart" variant="main">
                {{ __('common.roulette') }}
            </x-sidebar.link>

            <x-sidebar.link :href="localized_route('tags.index')" icon="bookmark" variant="main">
                {{ __('common.collection') }}
            </x-sidebar.link>

            <x-sidebar.link :href="localized_route('tags.index')" icon="bookmark" variant="main">
                {{ __('common.history') }}
            </x-sidebar.link>
        </nav>
    </div>

    <!-- TODO CHECK IF CHANGING THE TOKENS OF MY COMPOSITION CHANGES IT-->
    <x-sidebar.separator />

    <div class="sidebar-fade">
        @if(!empty($platforms))
            <x-sidebar.group :label="__('sidebar.cam_sites')" aria-label="cam sites">
                @foreach($platformData as $slug => $platform)
                    <x-sidebar.link :href="localized_route('platform.show', ['platform' => $slug])" icon="monitor"
                        :active="($activePlatform ?? null) === $slug" style="--sidebar-link-accent: {{ $platform['color'] }}">
                        {{ $platform['name'] }}
                    </x-sidebar.link>
                @endforeach
            </x-sidebar.group>

            <x-sidebar.separator />
        @endif

        <x-sidebar.group :label="__('sidebar.browse')" aria-label="browse">
            <x-sidebar.link :href="localized_route('niche.show', 'girls')">{{ __('common.girls') }}</x-sidebar.link>
            <x-sidebar.link :href="localized_route('niche.show', 'couples')">{{ __('common.couples') }}</x-sidebar.link>
            <x-sidebar.link :href="localized_route('niche.show', 'men')">{{ __('common.men') }}</x-sidebar.link>
            <x-sidebar.link :href="localized_route('niche.show', 'trans')">{{ __('common.trans') }}</x-sidebar.link>
            <x-sidebar.link :href="localized_route('countries.index')"
                icon="globe">{{ __('common.countries') }}</x-sidebar.link>
        </x-sidebar.group>

        @if(!empty($popularTags))
            <x-sidebar.group :label="__('sidebar.popular_tags')" aria-label="popular tags">
                @foreach($popularTags as $tag)
                    @php
                        $englishSlug = is_array($tag) ? ($tag['slug'] ?? '') : ($tag->slug ?? $tag);
                        $label = is_array($tag)
                            ? ($tag['name'] ?? \App\Models\Tag::localizeName($englishSlug))
                            : ($tag->localized_name ?? \App\Models\Tag::localizeName($englishSlug));
                        $localizedSlug = is_array($tag)
                            ? \App\Models\Tag::localizeSlug($englishSlug)
                            : ($tag->localized_slug ?? \App\Models\Tag::localizeSlug($englishSlug));
                        $counter = is_array($tag) ? ($tag['models_count'] ?? null) : ($tag->models_count ?? null);
                    @endphp

                    <x-sidebar.link :href="localized_route('tags.show', $localizedSlug)" :counter="$counter">
                        {{ $label }}
                    </x-sidebar.link>
                @endforeach
            </x-sidebar.group>
        @endif
    </div>

    <div class="sidebar-link-all-categories">
        <x-sidebar.link :href="localized_route('tags.index')" icon="tag" variant="button">
            Categories+
        </x-sidebar.link>
    </div>

    <x-sidebar.separator />

    <x-sidebar.policy :links="$policyLinks" />

    <x-sidebar.separator />

    <x-sidebar.language :label="config('locales.supported.' . app()->getLocale() . '.native', strtoupper(app()->getLocale()))" />
</x-sidebar.sidebar>

{{-- Sidebar-anchored panel: positioned to the right of the sidebar --}}
<x-panels.panel id="filter-panel" class="panel--sidebar-anchored" width="260px" highlight="accent" elevation="high" animation="slide">
    <x-panels.panel-head title="Filters" subtitle="Refine your results" />
    <x-panels.panel-divider />
    <x-panels.panel-list-item href="/tags">By Tag</x-panels.panel-list-item>
    <x-panels.panel-list-item href="/countries">By Country</x-panels.panel-list-item>
    <x-panels.panel-list-item href="/tags">By Age</x-panels.panel-list-item>
    <x-panels.panel-list-item href="/tags">By Status</x-panels.panel-list-item>
</x-panels.panel>