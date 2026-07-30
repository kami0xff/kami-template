{{--
    Index mode: OP + omitted count + recent replies.

    Props: id, omitted (int), href
    Slots: default (posts), omitted (custom omitted line)
--}}
@props([
    'id',
    'omitted' => 0,
    'href' => '#',
])

<article
    {{ $attributes->class(['forum-index-thread']) }}
    data-forum-index-thread
    data-thread-id="{{ $id }}"
>
    {{ $slot }}

    @if((int) $omitted > 0)
        <p class="forum-post-omitted">
            {{ (int) $omitted }} {{ (int) $omitted === 1 ? 'reply' : 'replies' }} omitted.
            <a href="{{ $href }}">Click here</a> to view.
        </p>
    @endif
</article>
