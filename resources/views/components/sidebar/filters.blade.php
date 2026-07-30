{{--
    Sidebar filter form (username / platform / age / sort).

    Use as: <x-sidebar.filters :filters="$filters" :platforms="$platforms" :genders="$genders" />

    Not the navbar Magic Search bar — that lives at <x-search> (components/search.blade.php).
--}}
<form method="GET" action="{{ $action ?? localized_route('home') }}" class="sidebar-search-form">
    <div class="sidebar-section sidebar-search">
        <div class="sidebar-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sidebar-search-icon">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="{{ __('filters.username_placeholder') }}" class="sidebar-search-input">
        </div>
    </div>
    {{-- Filters (collapsible) --}}
    <details class="sidebar-section sidebar-filters" {{ !empty(array_filter($filters, fn($v, $k) => $k !== 'search' && $v, ARRAY_FILTER_USE_BOTH)) ? 'open' : '' }}>
        <summary class="sidebar-section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
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
                            <option value="{{ $platform }}" {{ ($filters['platform'] ?? '') === $platform ? 'selected' : '' }}>
                                {{ ucfirst($platform) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="sidebar-field">
                <label>{{ __('filters.gender') }}</label>
                <select name="gender">
                    <option value="">{{ __('filters.all_genders') }}</option>
                    @foreach($genders as $gender)
                        <option value="{{ $gender }}" {{ ($filters['gender'] ?? '') === $gender ? 'selected' : '' }}>
                            {{ ucfirst($gender) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sidebar-field sidebar-field-row">
                <div>
                    <label>{{ __('filters.age_range') }}</label>
                    <div class="sidebar-age-row">
                        <input type="number" name="age_min" value="{{ $filters['age_min'] ?? '' }}"
                            placeholder="{{ __('filters.min') }}" min="18" max="99">
                        <span>-</span>
                        <input type="number" name="age_max" value="{{ $filters['age_max'] ?? '' }}"
                            placeholder="{{ __('filters.max') }}" min="18" max="99">
                    </div>
                </div>
            </div>
            <div class="sidebar-field">
                <label>{{ __('filters.sort_by') }}</label>
                <select name="sort">
                    <option value="viewers_count" {{ ($filters['sort'] ?? 'viewers_count') === 'viewers_count' ? 'selected' : '' }}>{{ __('filters.viewers') }}</option>
                    <option value="rating" {{ ($filters['sort'] ?? '') === 'rating' ? 'selected' : '' }}>
                        {{ __('filters.rating') }}
                    </option>
                    <option value="favorited_count" {{ ($filters['sort'] ?? '') === 'favorited_count' ? 'selected' : '' }}>{{ __('filters.favorites') }}</option>
                    <option value="age" {{ ($filters['sort'] ?? '') === 'age' ? 'selected' : '' }}>
                        {{ __('filters.age') }}
                    </option>
                </select>
            </div>
            <div class="sidebar-checkboxes">
                <label class="sidebar-checkbox"><input type="checkbox" name="online" value="1" {{ ($filters['online'] ?? false) ? 'checked' : '' }}><span>{{ __('filters.online') }}</span></label>
                <label class="sidebar-checkbox"><input type="checkbox" name="hd" value="1" {{ ($filters['hd'] ?? false) ? 'checked' : '' }}><span>{{ __('filters.hd') }}</span></label>
            </div>
            <div class="sidebar-filter-actions">
                <button type="submit" class="sidebar-filter-btn">{{ __('filters.apply_filters') }}</button>
                <a href="{{ $action ?? localized_route('home') }}"
                    class="sidebar-filter-clear">{{ __('filters.clear_filters') }}</a>
            </div>
        </div>
    </details>
</form>