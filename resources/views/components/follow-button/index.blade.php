{{--
    Follow / subscribe control.

    <x-follow-button :following="false" />
    <x-follow-button state="pending" label-pending="Requested" />
--}}
@props([
    'following' => false,
    'state' => null, // null | following | pending | follow
    'labelFollow' => 'Follow',
    'labelFollowing' => 'Following',
    'labelPending' => 'Requested',
    'variant' => 'primary',
])

@php
    $resolved = $state ?? ($following ? 'following' : 'follow');
    $label = match ($resolved) {
        'following' => $labelFollowing,
        'pending' => $labelPending,
        default => $labelFollow,
    };
    $btnVariant = $resolved === 'follow' ? $variant : 'muted';
@endphp

<button
    type="button"
    {{ $attributes->class([
        'follow-btn',
        'btn',
        'btn-' . $btnVariant,
        'follow-btn--' . $resolved,
    ]) }}
    data-follow-button
    data-state="{{ $resolved }}"
    aria-pressed="{{ $resolved === 'following' ? 'true' : 'false' }}"
>
    <span data-follow-label>{{ $label }}</span>
</button>
