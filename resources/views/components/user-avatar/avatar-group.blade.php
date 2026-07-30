{{--
    Overlapping avatar stack with a +N overflow chip.

    <x-user-avatar.avatar-group :avatars="['/img/a.jpg', '/img/b.jpg', null]" :max="4" :total="12" size="sm" />

    `avatars` is an array of image URLs (null renders the default avatar).
    `total` (optional) drives the +N chip; defaults to count of `avatars`.
--}}
@props([
    'avatars' => [],
    'max' => 4,
    'total' => null,
    'size' => 'md', // xs | sm | md
])

@php
    $shown = array_slice($avatars, 0, $max);
    $overflow = ($total ?? count($avatars)) - count($shown);
@endphp

<div {{ $attributes->class(['avatar-group', "avatar-group--$size" => $size !== 'md']) }}>
    @foreach($shown as $url)
        <x-user-avatar :url="$url" :size="$size" />
    @endforeach
    @if($overflow > 0)
        <span class="avatar-group-more">+{{ $overflow }}</span>
    @endif
</div>
