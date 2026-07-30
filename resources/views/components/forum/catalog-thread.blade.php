{{--
    Catalog thread tile.

    Props: id, href, thumb, alt, subject, teaser, images, replies, sticky, locked
--}}
@props([
    'id',
    'href' => '#',
    'thumb' => null,
    'alt' => '',
    'subject' => null,
    'teaser' => null,
    'images' => 0,
    'replies' => 0,
    'sticky' => false,
    'locked' => false,
])

<article
    {{ $attributes->class(['forum-catalog-thread', 'is-sticky' => $sticky]) }}
    data-forum-catalog-thread
    data-thread-id="{{ $id }}"
>
    <div class="forum-catalog-thumb">
        @if($thumb)
            <a href="{{ $href }}">
                <img src="{{ $thumb }}" alt="{{ $alt }}" loading="lazy" decoding="async">
            </a>
        @else
            <a href="{{ $href }}" class="forum-empty" style="padding:18px 8px; width:100%;">No file</a>
        @endif
    </div>

    <div class="forum-catalog-stats">
        R: <strong>{{ (int) $replies }}</strong>
        / I: <strong>{{ (int) $images }}</strong>
    </div>

    @if($sticky || $locked)
        <div class="forum-catalog-flags">
            @if($sticky)<span class="forum-flag forum-flag--sticky">Sticky</span>@endif
            @if($locked)<span class="forum-flag forum-flag--locked">Locked</span>@endif
        </div>
    @endif

    @if($subject)
        <h3 class="forum-catalog-subject"><a href="{{ $href }}">{{ $subject }}</a></h3>
    @endif

    @if($teaser)
        <p class="forum-catalog-teaser">{{ $teaser }}</p>
    @endif
</article>
