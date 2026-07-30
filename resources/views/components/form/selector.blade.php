{{--
    Searchable selector (Astryx Selector / combobox).

    <x-form.selector
        name="tag"
        placeholder="Pick a tag…"
        :options="['a' => 'Option A', 'b' => 'Option B']"
        selected="a"
        :searchable="true"
    />

    Options may also be a list of arrays:
        [['value' => 'fr', 'label' => 'France', 'meta' => 'EU', 'icon' => '<span class="fi fi-fr">']]
--}}
@props([
    'name' => '',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Select…',
    'searchable' => true,
    'searchPlaceholder' => 'Search…',
    'disabled' => false,
    'emptyText' => 'No matches',
])

@php
    $current = (string) old($name, $selected);
    $normalized = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $normalized[] = [
                'value' => (string) ($opt['value'] ?? ''),
                'label' => (string) ($opt['label'] ?? $opt['value'] ?? ''),
                'meta' => $opt['meta'] ?? null,
                'icon' => $opt['icon'] ?? null,
            ];
        } else {
            $normalized[] = [
                'value' => (string) $key,
                'label' => (string) $opt,
                'meta' => null,
                'icon' => null,
            ];
        }
    }
    $selectedLabel = collect($normalized)->firstWhere('value', $current)['label'] ?? null;
@endphp

<div
    {{ $attributes->class(['form-selector']) }}
    data-selector
    data-searchable="{{ $searchable ? 'true' : 'false' }}"
    @if($disabled) data-disabled="true" @endif
>
    <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-selector-value>

    <button
        type="button"
        class="form-selector-trigger"
        data-selector-trigger
        @disabled($disabled)
        aria-haspopup="listbox"
        aria-expanded="false"
    >
        <span class="form-selector-value {{ $selectedLabel ? '' : 'is-placeholder' }}" data-selector-label>
            {{ $selectedLabel ?: $placeholder }}
        </span>
        <svg class="form-selector-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </button>

    <div class="form-selector-panel" data-selector-panel hidden>
        @if($searchable)
            <div class="form-selector-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
                <input
                    type="search"
                    class="form-selector-search-input"
                    data-selector-search
                    placeholder="{{ $searchPlaceholder }}"
                    autocomplete="off"
                >
            </div>
        @endif
        <ul class="form-selector-list" data-selector-list role="listbox">
            @foreach($normalized as $opt)
                <li
                    class="form-selector-option {{ $opt['value'] === $current ? 'is-selected' : '' }}"
                    role="option"
                    data-selector-option
                    data-value="{{ $opt['value'] }}"
                    data-label="{{ $opt['label'] }}"
                    aria-selected="{{ $opt['value'] === $current ? 'true' : 'false' }}"
                >
                    @if($opt['icon'])
                        <span class="form-selector-option-icon">{!! $opt['icon'] !!}</span>
                    @endif
                    <span class="form-selector-option-label">{{ $opt['label'] }}</span>
                    @if($opt['meta'])
                        <span class="form-selector-option-meta">{{ $opt['meta'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="form-selector-empty" data-selector-empty hidden>{{ $emptyText }}</div>
    </div>
</div>
