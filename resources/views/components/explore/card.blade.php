{{--
    Mixed-media explore card.

    Props:
        type: live|image|gallery|gif|video|manga|ai
        href, src, poster, alt, title, author, avatar,
        items: array of image URLs (or ['src'=>, 'alt'=>]) for gallery/manga horizontal pages
        categories, viewers, duration, pages, count, live
--}}
@props([
    'type' => 'image',
    'href' => '#',
    'src' => null,
    'poster' => null,
    'alt' => '',
    'title' => null,
    'author' => null,
    'avatar' => null,
    'items' => [],
    'categories' => [],
    'viewers' => null,
    'duration' => null,
    'pages' => null,
    'count' => null,
    'live' => false,
])

@php
    $type = strtolower((string) $type);
    $catIds = [];
    $catLabels = [];
    foreach ($categories as $c) {
        if (is_array($c)) {
            $catIds[] = (string) ($c['id'] ?? '');
            $catLabels[] = (string) ($c['label'] ?? $c['id'] ?? '');
        } else {
            $catIds[] = (string) $c;
            $catLabels[] = (string) $c;
        }
    }
    $catIds = array_values(array_filter($catIds));
    $isLive = $live || $type === 'live';
    $isPaged = in_array($type, ['gallery', 'manga'], true);

    $pageItems = [];
    foreach ($items as $item) {
        if (is_array($item)) {
            $pageItems[] = [
                'src' => (string) ($item['src'] ?? $item['url'] ?? ''),
                'alt' => (string) ($item['alt'] ?? $alt),
            ];
        } elseif (is_string($item) && $item !== '') {
            $pageItems[] = ['src' => $item, 'alt' => $alt];
        }
    }
    if ($isPaged && count($pageItems) === 0 && ($src || $poster)) {
        $pageItems[] = ['src' => (string) ($src ?: $poster), 'alt' => $alt];
    }

    $pageCount = max(count($pageItems), (int) ($pages ?? 0), (int) ($count ?? 0), $isPaged ? 1 : 0);
    if ($isPaged && count($pageItems) > 0) {
        $pageCount = count($pageItems);
    }

    $mediaSrc = $src ?: $poster;
    $badge = match ($type) {
        'live' => 'LIVE',
        'gif' => 'GIF',
        'video' => $duration ?: 'VIDEO',
        'gallery' => $pageCount > 1 ? "{$pageCount} photos" : 'GALLERY',
        'manga' => $pageCount > 1 ? "{$pageCount} pages" : 'MANGA',
        'ai' => 'AI',
        default => 'PHOTO',
    };
@endphp

<article
    {{ $attributes->class(['explore-card', "explore-card--{$type}", 'explore-card--paged' => $isPaged && $pageCount > 1]) }}
    data-explore-card
    data-type="{{ $type }}"
    data-categories="{{ implode(',', $catIds) }}"
    @if($isPaged) data-explore-paged @endif
>
    <div class="explore-card-media" data-explore-media>
        @if($isPaged && count($pageItems) > 0)
            <div
                class="explore-pages"
                data-explore-pages
                tabindex="0"
                role="region"
                aria-label="{{ $type === 'manga' ? 'Manga pages' : 'Gallery photos' }}"
            >
                @foreach($pageItems as $i => $page)
                    <div class="explore-page" data-explore-page="{{ $i + 1 }}">
                        <img
                            src="{{ $page['src'] }}"
                            alt="{{ $page['alt'] !== '' ? $page['alt'] : ($title ? $title.' — '.($i + 1) : 'Page '.($i + 1)) }}"
                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                            decoding="async"
                            draggable="false"
                        >
                    </div>
                @endforeach
            </div>
        @elseif(in_array($type, ['video', 'gif'], true) && $src)
            <video
                src="{{ $src }}"
                @if($poster) poster="{{ $poster }}" @endif
                muted
                loop
                playsinline
                preload="metadata"
                data-explore-video
            ></video>
        @elseif($mediaSrc)
            <img src="{{ $mediaSrc }}" alt="{{ $alt }}" loading="lazy" decoding="async">
        @endif

        <div class="explore-card-chrome" data-explore-chrome>
            <div class="explore-card-badges">
                <span class="explore-card-badge explore-card-badge--{{ $type }}{{ $isLive ? ' explore-card-badge--live' : '' }}">
                    {{ $isLive && $type === 'live' ? 'LIVE' : $badge }}
                </span>
            </div>

            @if($isPaged && $pageCount > 1)
                <button
                    type="button"
                    class="explore-page-nav explore-page-nav--prev"
                    data-explore-page-prev
                    aria-label="Previous page"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M15 6 9 12l6 6"/></svg>
                </button>
                <button
                    type="button"
                    class="explore-page-nav explore-page-nav--next"
                    data-explore-page-next
                    aria-label="Next page"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                </button>

                <div class="explore-card-pager" data-explore-pager>
                    <span class="explore-card-pages" data-explore-page-label>1 / {{ $pageCount }}</span>
                    <div class="explore-card-dots" data-explore-dots aria-hidden="true">
                        @foreach($pageItems as $i => $page)
                            <span class="explore-card-dot{{ $i === 0 ? ' is-active' : '' }}" data-explore-dot="{{ $i }}"></span>
                        @endforeach
                    </div>
                    <span class="explore-card-swipe-hint">Swipe for more</span>
                </div>
            @elseif($type === 'gallery')
                <span class="explore-card-stack" aria-hidden="true"></span>
            @endif

            @if(in_array($type, ['video', 'live'], true))
                <div class="explore-card-play" aria-hidden="true">
                    <span>
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                </div>
            @endif

            <div class="explore-card-meta-float">
                @if($title)
                    <h3 class="explore-card-title">
                        <a href="{{ $href }}" data-explore-open>{{ $title }}</a>
                    </h3>
                @endif
                <div class="explore-card-sub">
                    @if($author)
                        <span class="explore-card-author">
                            @if($avatar)
                                <img src="{{ $avatar }}" alt="" loading="lazy">
                            @endif
                            {{ $author }}
                        </span>
                    @endif
                    @if($isLive && !is_null($viewers))
                        <span>{{ number_format((int) $viewers) }} watching</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="explore-card-foot">
        <div class="explore-card-cats">
            @foreach($catLabels as $label)
                @if($label !== '')
                    <span class="explore-card-cat">{{ $label }}</span>
                @endif
            @endforeach
        </div>
        @if($isPaged && $pageCount)
            <span class="explore-card-stat" data-explore-page-stat>1 / {{ $pageCount }}</span>
        @elseif($type === 'video' && $duration)
            <span class="explore-card-stat">{{ $duration }}</span>
        @elseif($isLive && !is_null($viewers))
            <span class="explore-card-stat">{{ number_format((int) $viewers) }}</span>
        @endif
    </div>
</article>
