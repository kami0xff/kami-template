@props(['popularTags' => [], 'filters' => [], 'platforms' => [], 'genders' => [], 'action' => null, 'activePlatform' => null])

<aside class="sidebar" id="sidebar">
    {{-- Search --}}
    <form method="GET" action="{{ $action ?? localized_route('home') }}" class="sidebar-search-form">
        <div class="sidebar-section sidebar-search">
            <div class="sidebar-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sidebar-search-icon">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('filters.username_placeholder') }}" class="sidebar-search-input">
            </div>
        </div>

        {{-- Filters (collapsible) --}}
        <details class="sidebar-section sidebar-filters" {{ !empty(array_filter($filters, fn($v, $k) => $k !== 'search' && $v, ARRAY_FILTER_USE_BOTH)) ? 'open' : '' }}>
            <summary class="sidebar-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                {{ __('filters.filters') }}
            </summary>
            <div class="sidebar-filter-fields">
                @if($platforms->count() > 1)
                <div class="sidebar-field">
                    <label>{{ __('filters.platform') }}</label>
                    <select name="platform">
                        <option value="">{{ __('filters.all_platforms') }}</option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform }}" {{ ($filters['platform'] ?? '') === $platform ? 'selected' : '' }}>{{ ucfirst($platform) }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="sidebar-field">
                    <label>{{ __('filters.gender') }}</label>
                    <select name="gender">
                        <option value="">{{ __('filters.all_genders') }}</option>
                        @foreach($genders as $gender)
                            <option value="{{ $gender }}" {{ ($filters['gender'] ?? '') === $gender ? 'selected' : '' }}>{{ ucfirst($gender) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sidebar-field sidebar-field-row">
                    <div>
                        <label>{{ __('filters.age_range') }}</label>
                        <div class="sidebar-age-row">
                            <input type="number" name="age_min" value="{{ $filters['age_min'] ?? '' }}" placeholder="{{ __('filters.min') }}" min="18" max="99">
                            <span>-</span>
                            <input type="number" name="age_max" value="{{ $filters['age_max'] ?? '' }}" placeholder="{{ __('filters.max') }}" min="18" max="99">
                        </div>
                    </div>
                </div>
                <div class="sidebar-field">
                    <label>{{ __('filters.sort_by') }}</label>
                    <select name="sort">
                        <option value="viewers_count" {{ ($filters['sort'] ?? 'viewers_count') === 'viewers_count' ? 'selected' : '' }}>{{ __('filters.viewers') }}</option>
                        <option value="rating" {{ ($filters['sort'] ?? '') === 'rating' ? 'selected' : '' }}>{{ __('filters.rating') }}</option>
                        <option value="favorited_count" {{ ($filters['sort'] ?? '') === 'favorited_count' ? 'selected' : '' }}>{{ __('filters.favorites') }}</option>
                        <option value="age" {{ ($filters['sort'] ?? '') === 'age' ? 'selected' : '' }}>{{ __('filters.age') }}</option>
                    </select>
                </div>
                <div class="sidebar-checkboxes">
                    <label class="sidebar-checkbox"><input type="checkbox" name="online" value="1" {{ ($filters['online'] ?? false) ? 'checked' : '' }}><span>{{ __('filters.online') }}</span></label>
                    <label class="sidebar-checkbox"><input type="checkbox" name="hd" value="1" {{ ($filters['hd'] ?? false) ? 'checked' : '' }}><span>{{ __('filters.hd') }}</span></label>
                </div>
                <div class="sidebar-filter-actions">
                    <button type="submit" class="sidebar-filter-btn">{{ __('filters.apply_filters') }}</button>
                    <a href="{{ $action ?? localized_route('home') }}" class="sidebar-filter-clear">{{ __('filters.clear_filters') }}</a>
                </div>
            </div>
        </details>
    </form>

    {{-- Cam Sites --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
            {{ __('sidebar.cam_sites') }}
        </div>
        <div class="sidebar-platforms">
            @php
                $platformData = [
                    'stripchat' => ['name' => 'Stripchat', 'color' => '#903232', 'logo' => 'stripchat-logo.svg'],
                    'chaturbate' => ['name' => 'Chaturbate', 'color' => '#F57C00', 'logo' => 'chaturbate-logo.svg'],
                    'bongacams' => ['name' => 'BongaCams', 'color' => '#E91E63', 'logo' => 'bongacams-logo.svg'],
                    'xlovecam' => ['name' => 'XloveCam', 'color' => '#9C27B0', 'logo' => 'xlovecam-logo.svg'],
                ];
            @endphp
            @foreach($platformData as $slug => $pdata)
                <a href="{{ localized_route('platform.show', ['platform' => $slug]) }}" class="sidebar-platform-link {{ $activePlatform === $slug ? 'active' : '' }}" style="--p-color: {{ $pdata['color'] }}">
                    <img src="{{ asset($pdata['logo']) }}" alt="{{ $pdata['name'] }}" class="sidebar-platform-icon">
                    <span>{{ $pdata['name'] }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sidebar-platform-arrow"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Popular Tags --}}
    @if(!empty($popularTags))
    <div class="sidebar-section">
        <div class="sidebar-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
            </svg>
            {{ __('sidebar.popular_tags') }}
        </div>
        <div class="sidebar-tags">
            @foreach($popularTags as $tag)
                <a href="{{ localized_route('tags.show', $tag) }}" class="sidebar-tag">{{ $tag }}</a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Browse --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
            </svg>
            {{ __('sidebar.browse') }}
        </div>
        <nav class="sidebar-nav">
            <a href="{{ localized_route('home') }}" class="sidebar-nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                {{ __('common.home') }}
            </a>
            <a href="{{ localized_route('niche.show', 'girls') }}" class="sidebar-nav-link">{{ __('common.girls') }}</a>
            <a href="{{ localized_route('niche.show', 'couples') }}" class="sidebar-nav-link">{{ __('common.couples') }}</a>
            <a href="{{ localized_route('niche.show', 'men') }}" class="sidebar-nav-link">{{ __('common.men') }}</a>
            <a href="{{ localized_route('niche.show', 'trans') }}" class="sidebar-nav-link">{{ __('common.trans') }}</a>
            <a href="{{ localized_route('tags.index') }}" class="sidebar-nav-link">{{ __('common.all_tags') }}</a>
            <a href="{{ localized_route('countries.index') }}" class="sidebar-nav-link">{{ __('common.countries') }}</a>
            <a href="{{ localized_route('explore') }}" class="sidebar-nav-link">{{ __('common.explore') }}</a>
            <a href="{{ localized_route('roulette') }}" class="sidebar-nav-link">{{ __('common.roulette') }}</a>
        </nav>
    </div>
</aside>
