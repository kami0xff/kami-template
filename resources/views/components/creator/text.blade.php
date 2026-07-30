{{--
    Text / about blurb.

    Props: editable (show textarea in edit mode via manager)
--}}
@props([
    'editable' => false,
])

@if($editable)
    <p class="creator-text" data-creator-view-only>{{ $slot }}</p>
    <textarea
        class="creator-text-input"
        data-creator-edit-only
        data-creator-text-input
        rows="3"
    >{{ trim($slot) }}</textarea>
@else
    <p {{ $attributes->class(['creator-text']) }}>{{ $slot }}</p>
@endif
