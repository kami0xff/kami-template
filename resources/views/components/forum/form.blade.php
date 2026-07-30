{{--
    New thread / reply form (imageboard style).

    Props: mode (thread|reply), action, method, threadId
--}}
@props([
    'mode' => 'reply',
    'action' => '#',
    'method' => 'post',
    'threadId' => null,
])

@php
    $isThread = $mode === 'thread';
    $title = $isThread ? 'Start a new thread' : 'Reply to thread';
    $submit = $isThread ? 'Post thread' : 'Post reply';
@endphp

<form
    {{ $attributes->class(['forum-form']) }}
    data-forum-form
    data-forum-form-mode="{{ $mode }}"
    @if($threadId) data-thread-id="{{ $threadId }}" @endif
    action="{{ $action }}"
    method="{{ $method }}"
>
    <h3 class="forum-form-title">{{ $title }}</h3>

    <div class="forum-form-row">
        <label for="forum-name-{{ $threadId ?? 'new' }}">Name</label>
        <input
            id="forum-name-{{ $threadId ?? 'new' }}"
            type="text"
            name="name"
            placeholder="Anonymous"
            autocomplete="nickname"
            data-forum-name
        >
    </div>

    <div class="forum-form-row">
        <label for="forum-options-{{ $threadId ?? 'new' }}">Options</label>
        <input
            id="forum-options-{{ $threadId ?? 'new' }}"
            type="text"
            name="options"
            placeholder="sage"
            data-forum-options
        >
    </div>

    @if($isThread)
        <div class="forum-form-row">
            <label for="forum-subject-{{ $threadId ?? 'new' }}">Subject</label>
            <input
                id="forum-subject-{{ $threadId ?? 'new' }}"
                type="text"
                name="subject"
                data-forum-subject
            >
        </div>
    @endif

    <div class="forum-form-row">
        <label for="forum-comment-{{ $threadId ?? 'new' }}">Comment</label>
        <textarea
            id="forum-comment-{{ $threadId ?? 'new' }}"
            name="comment"
            required
            data-forum-comment-input
            placeholder="{{ $isThread ? 'Say something…' : '>>quote with No. links' }}"
        ></textarea>
        <p class="forum-form-hint">Lines starting with &gt; become greentext. Click a post number to quote.</p>
    </div>

    <div class="forum-form-row">
        <label for="forum-file-{{ $threadId ?? 'new' }}">File</label>
        <div>
            <input
                id="forum-file-{{ $threadId ?? 'new' }}"
                type="file"
                name="file"
                accept="image/*,video/*"
                data-forum-file
            >
            <img class="forum-form-preview" data-forum-file-preview alt="">
        </div>
    </div>

    <div class="forum-form-actions">
        <button type="submit">{{ $submit }}</button>
    </div>
</form>
