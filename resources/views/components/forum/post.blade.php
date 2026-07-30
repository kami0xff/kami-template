{{--
    Single imageboard post (OP or reply).

    Props:
        id, name, trip, subject, time, datetime,
        fileName, fileSize, fileDims, thumb, image, alt,
        op, sticky, locked, backlinks (array of post ids),
        omitted, threadHref
    Slot: comment body (supports greentext / quote links)
--}}
@props([
    'id',
    'name' => 'Anonymous',
    'trip' => null,
    'subject' => null,
    'time' => null,
    'datetime' => null,
    'fileName' => null,
    'fileSize' => null,
    'fileDims' => null,
    'thumb' => null,
    'image' => null,
    'alt' => '',
    'op' => false,
    'sticky' => false,
    'locked' => false,
    'backlinks' => [],
    'omitted' => null,
    'threadHref' => null,
])

@php
    $media = $image ?: $thumb;
@endphp

<article
    {{ $attributes->class(['forum-post', 'forum-post--op' => $op, 'forum-post--reply' => !$op]) }}
    data-forum-post
    data-post-id="{{ $id }}"
    id="p{{ $id }}"
>
    @if($fileName || $thumb || $image)
        <div class="forum-post-file">
            File:
            @if($media)
                <a href="{{ $image ?: $thumb }}" target="_blank" rel="noopener" data-forum-file-link>{{ $fileName ?? 'image' }}</a>
            @else
                <span>{{ $fileName }}</span>
            @endif
            @if($fileSize || $fileDims)
                <span>
                    (@if($fileSize){{ $fileSize }}@endif
                    @if($fileSize && $fileDims), @endif
                    @if($fileDims){{ $fileDims }}@endif)
                </span>
            @endif
        </div>
    @endif

    <div class="forum-post-body">
        @if($thumb || $image)
            <div class="forum-post-thumb">
                <a
                    href="{{ $image ?: $thumb }}"
                    data-forum-thumb
                >
                    <img
                        src="{{ $thumb ?: $image }}"
                        data-full="{{ $image ?: $thumb }}"
                        alt="{{ $alt }}"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
            </div>
        @endif

        <div class="forum-post-content">
            <header class="forum-post-head">
                @if($subject)
                    <span class="forum-post-subject">{{ $subject }}</span>
                @endif

                <span class="forum-post-name">{{ $name }}</span>

                @if($trip)
                    <span class="forum-post-trip">{{ $trip }}</span>
                @endif

                @if($sticky || $locked)
                    <span class="forum-post-flags">
                        @if($sticky)<span class="forum-flag forum-flag--sticky">Sticky</span>@endif
                        @if($locked)<span class="forum-flag forum-flag--locked">Locked</span>@endif
                    </span>
                @endif

                @if($time)
                    <time class="forum-post-time" @if($datetime) datetime="{{ $datetime }}" @endif>{{ $time }}</time>
                @endif

                <a
                    href="#p{{ $id }}"
                    class="forum-post-id"
                    data-forum-quote
                    data-quote-id="{{ $id }}"
                    title="Quote post No.{{ $id }}"
                >No.{{ $id }}</a>

                @if(!empty($backlinks))
                    <span class="forum-post-backlinks" data-forum-backlinks>
                        @foreach($backlinks as $linkId)
                            <a class="forum-backlink" href="#p{{ $linkId }}" data-forum-quote-link>&gt;&gt;{{ $linkId }}</a>
                        @endforeach
                    </span>
                @endif
            </header>

            <div class="forum-post-comment" data-forum-comment>
                {{ $slot }}
            </div>

            @if(!is_null($omitted) && (int) $omitted > 0)
                <p class="forum-post-omitted">
                    {{ (int) $omitted }} {{ (int) $omitted === 1 ? 'reply' : 'replies' }} omitted.
                    @if($threadHref)
                        <a href="{{ $threadHref }}">Click here</a> to view.
                    @endif
                </p>
            @endif
        </div>
    </div>
</article>
