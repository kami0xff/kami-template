{{--
    Full thread view shell.

    Props: id, board
    Slots: toolbar, form, default (posts)
--}}
@props([
    'id',
    'board' => null,
])

<section
    {{ $attributes->class(['forum-thread']) }}
    data-forum-thread
    data-thread-id="{{ $id }}"
    @if($board) data-board="{{ $board }}" @endif
>
    @isset($toolbar)
        <div class="forum-thread-toolbar">
            {{ $toolbar }}
        </div>
    @endisset

    <div class="forum-thread-posts" data-forum-posts>
        {{ $slot }}
    </div>

    @isset($form)
        {{ $form }}
    @endisset
</section>
