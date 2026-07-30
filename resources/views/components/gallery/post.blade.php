{{--
    Lightweight gallery post shell (demo / feed item).

    Props: author, avatar, time, image, alt, caption
    Slot: reactions (defaults to empty reactions bar)
--}}
@props([
    'author',
    'avatar' => null,
    'time' => null,
    'image',
    'alt' => '',
    'caption' => null,
])

<article {{ $attributes->class(['gallery-post']) }} data-gallery-post>
    <div class="gallery-post-meta">
        <x-user-avatar :url="$avatar" :name="$author" size="sm" />
        <strong>{{ $author }}</strong>
        @if($time)
            <span>{{ $time }}</span>
        @endif
    </div>

    <div class="gallery-post-media">
        <img src="{{ $image }}" alt="{{ $alt }}" loading="lazy" decoding="async">
    </div>

    @if($caption)
        <p class="gallery-post-caption">{{ $caption }}</p>
    @endif

    <div class="gallery-post-reactions">
        @isset($reactions)
            {{ $reactions }}
        @else
            <x-reactions.bar />
        @endisset
    </div>
</article>
