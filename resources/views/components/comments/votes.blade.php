{{--
    Vote rail — up / score / down.

    Props:
        score  — integer score
        value  — current user vote: 1 | 0 | -1
--}}
@props([
    'score' => 0,
    'value' => 0,
])

@php
    $value = (int) $value;
    $tone = $value > 0 ? 'up' : ($value < 0 ? 'down' : '');
@endphp

<div class="comment-vote" data-comment-vote data-score="{{ (int) $score }}" data-value="{{ $value }}">
    <button
        type="button"
        class="comment-vote-btn comment-vote-btn--up"
        data-comment-upvote
        aria-label="Upvote"
        aria-pressed="{{ $value === 1 ? 'true' : 'false' }}"
        title="Upvote"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 19V5M5 12l7-7 7 7" />
        </svg>
    </button>
    <span class="comment-score" data-comment-score @if($tone) data-tone="{{ $tone }}" @endif>{{ (int) $score }}</span>
    <button
        type="button"
        class="comment-vote-btn comment-vote-btn--down"
        data-comment-downvote
        aria-label="Downvote"
        aria-pressed="{{ $value === -1 ? 'true' : 'false' }}"
        title="Downvote"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 5v14M19 12l-7 7-7-7" />
        </svg>
    </button>
</div>
