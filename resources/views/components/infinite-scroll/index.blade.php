{{--
    Infinite scroll for feeds — sentinel-driven load more (or button-only).

    Props:
        page         — current page (1-based, default 1)
        hasMore      — whether another page exists
        mode         — auto | button  (auto uses IntersectionObserver; reduced-motion → button)
        url          — optional fetch URL with {page} placeholder (HTML fragment or JSON { html, hasMore })
        rootMargin   — IntersectionObserver rootMargin
        loadingText / endText / errorText / buttonLabel
        ariaLabel

    Events (bubble from root):
        infinite-scroll:load  { page, root, done({ html?, hasMore? }), fail(message?) }

    Optional attrs:
        data-scroll-root="viewport|#sel" — observer root (default: nearest overflow ancestor)
        data-demo / data-demo-max — design-kit fake pages

    <x-infinite-scroll :page="1" :has-more="true">
        <article>…</article>
    </x-infinite-scroll>
--}}
@props([
    'page' => 1,
    'hasMore' => true,
    'mode' => 'auto',
    'url' => null,
    'rootMargin' => '240px 0px',
    'loadingText' => 'Loading more…',
    'endText' => "You're all caught up",
    'errorText' => 'Couldn’t load more. Try again.',
    'buttonLabel' => 'Load more',
    'ariaLabel' => 'Feed',
])

@php
    $mode = in_array($mode, ['auto', 'button'], true) ? $mode : 'auto';
@endphp

<div
    {{ $attributes->class(['infinite-scroll']) }}
    data-infinite-scroll
    data-page="{{ (int) $page }}"
    data-has-more="{{ $hasMore ? '1' : '0' }}"
    data-mode="{{ $mode }}"
    data-root-margin="{{ $rootMargin }}"
    data-loading-text="{{ $loadingText }}"
    data-end-text="{{ $endText }}"
    data-error-text="{{ $errorText }}"
    @if($url) data-url="{{ $url }}" @endif
    role="feed"
    aria-label="{{ $ariaLabel }}"
    aria-busy="false"
>
    <div class="infinite-scroll-items" data-infinite-scroll-items>
        {{ $slot }}
    </div>

    <div class="infinite-scroll-footer">
        <div class="infinite-scroll-status" data-infinite-scroll-status hidden>
            <span class="spinner spinner--sm" aria-hidden="true"></span>
            <span data-infinite-scroll-status-text>{{ $loadingText }}</span>
        </div>

        <p class="infinite-scroll-end" data-infinite-scroll-end @if($hasMore) hidden @endif>
            {{ $endText }}
        </p>

        <div class="infinite-scroll-error" data-infinite-scroll-error hidden>
            <span data-infinite-scroll-error-text>{{ $errorText }}</span>
            <button type="button" class="infinite-scroll-retry" data-infinite-scroll-retry>
                Retry
            </button>
        </div>

        <button
            type="button"
            class="infinite-scroll-more"
            data-infinite-scroll-more
            @if(! $hasMore) hidden @endif
        >
            {{ $buttonLabel }}
        </button>

        <div
            class="infinite-scroll-sentinel"
            data-infinite-scroll-sentinel
            aria-hidden="true"
        ></div>
    </div>
</div>
