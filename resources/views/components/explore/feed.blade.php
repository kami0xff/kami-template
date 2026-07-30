{{--
    Feed container.

    Props: layout strip|grid (inherits from parent explore if omitted)
--}}
@props([
    'layout' => null,
])

@php
    $layoutClass = $layout ? "explore-feed--{$layout}" : '';
@endphp

<div
    {{ $attributes->class(['explore-feed', $layoutClass]) }}
    data-explore-feed
    @if($layout) data-explore-feed-layout="{{ $layout }}" @endif
>
    {{ $slot }}

    <div class="explore-empty" data-explore-empty hidden>
        <strong>Nothing matches</strong>
        Turn on a media type or clear category filters to keep scrolling.
    </div>

    <div class="explore-sentinel" data-explore-sentinel aria-hidden="true"></div>
    <p class="explore-status" data-explore-status hidden>Loading more…</p>
</div>
