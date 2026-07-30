{{--
    Category filter row (secondary — refine after media types).

    Props:
        categories — [['id','label','count'?], ...]
        requireTypes — when true, dims until ≥1 media type is on (default true)
--}}
@props([
    'categories' => [],
    'requireTypes' => true,
])

<div
    class="explore-bar-row explore-cats-wrap"
    data-explore-cats-row
    @if($requireTypes) data-require-types="true" @endif
>
    <span class="explore-bar-label">Cats</span>
    <div class="explore-scroller" role="group" aria-label="Categories">
        <div class="explore-cats" data-explore-cats>
            <button
                type="button"
                class="explore-chip is-on"
                data-explore-cat="*"
                aria-pressed="true"
            >
                <span>All</span>
            </button>
            @foreach($categories as $cat)
                @php
                    $id = is_array($cat) ? ($cat['id'] ?? '') : (string) $cat;
                    $label = is_array($cat) ? ($cat['label'] ?? $id) : (string) $cat;
                    $count = is_array($cat) ? ($cat['count'] ?? null) : null;
                @endphp
                <button
                    type="button"
                    class="explore-chip is-off"
                    data-explore-cat="{{ $id }}"
                    aria-pressed="false"
                >
                    <span>{{ $label }}</span>
                    @if(!is_null($count))
                        <span class="explore-chip-count">{{ number_format((int) $count) }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <p class="explore-cats-hint" data-explore-cats-hint hidden>Pick a media type first</p>
</div>
