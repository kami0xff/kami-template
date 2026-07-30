{{--
    Single comment (or reply).

    Props:
        id, author, avatar, time, body, score, vote (1|0|-1), replyable

    Slots:
        replies — nested reply items (one level)
--}}
@props([
    'id',
    'author',
    'avatar' => null,
    'time' => null,
    'body' => '',
    'score' => 0,
    'vote' => 0,
    'replyable' => true,
])

<article
    {{ $attributes->class(['comment', 'comment--reply' => !$replyable]) }}
    data-comment
    data-comment-id="{{ $id }}"
    id="comment-{{ $id }}"
>
    <x-comments.votes :score="$score" :value="$vote" />

    <div class="comment-main">
        <header class="comment-head">
            <x-user-avatar :url="$avatar" :name="$author" size="xs" />
            <a href="#" class="comment-author">{{ $author }}</a>
            @if($time)
                <time class="comment-meta" datetime="{{ $time }}">{{ $time }}</time>
            @endif
        </header>

        <p class="comment-body">{{ $body }}</p>

        <div class="comment-actions">
            @if($replyable)
                <button type="button" class="comment-action" data-comment-reply aria-controls="reply-composer-{{ $id }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M9 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5" />
                        <path d="m15 19 5-5-5-5M20 14H10" />
                    </svg>
                    Reply
                </button>
            @endif
            <button type="button" class="comment-action" data-comment-share>
                Share
            </button>
        </div>

        @if($replyable)
            <div class="comment-reply-composer" id="reply-composer-{{ $id }}" data-comment-reply-slot hidden>
                <x-comments.composer
                    :reply-to="$id"
                    placeholder="Write a reply…"
                    submit-label="Reply"
                    :name="'You'"
                    avatar="https://i.pravatar.cc/64?img=5"
                />
            </div>
        @endif

        @isset($replies)
            <div class="comment-replies" data-comment-replies>
                {{ $replies }}
            </div>
        @endisset
    </div>
</article>
