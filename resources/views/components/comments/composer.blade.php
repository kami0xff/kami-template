{{--
    Comment composer (top-level or reply).

    Props:
        placeholder, submitLabel, avatar, name, replyTo (parent id for replies)
--}}
@props([
    'placeholder' => 'Add a comment…',
    'submitLabel' => 'Comment',
    'avatar' => null,
    'name' => 'You',
    'replyTo' => null,
])

<form
    {{ $attributes->class(['comments-composer']) }}
    data-comment-composer
    @if($replyTo) data-comment-reply-to="{{ $replyTo }}" @endif
>
    <div class="comments-composer-row">
        <x-user-avatar :url="$avatar" :name="$name" size="sm" />
        <textarea
            class="comments-composer-input"
            name="body"
            rows="3"
            placeholder="{{ $placeholder }}"
            data-comment-input
            required
        ></textarea>
    </div>
    <div class="comments-composer-actions">
        @if($replyTo)
            <button type="button" class="btn btn-muted btn-sm" data-comment-cancel-reply>Cancel</button>
        @endif
        <button type="submit" class="btn btn-primary btn-sm">{{ $submitLabel }}</button>
    </div>
</form>
