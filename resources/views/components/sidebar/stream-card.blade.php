@props(['streamer' => []])

@php

    $skeletonDisplay = false;
    if (empty($streamer)) {
        $skeletonDisplay = true;
        // error no data passed in display defaults 
    }
    $username = $streamer['username'] ?? 'default';
    $viewers = $streamer['viewers'] ?? 0;
    $avatar = $streamer['avatar'] ?? 'default-avatar.png';

    //localised url utility function to be used here
    //default should be en
    $href = "https://gay.cam/$username"
@endphp


@if ($skeletonDisplay)
    <a class="streamer-card-skeleton">
        <div class="streamer-card-skeleton-avatar">

        </div>
        <div class="streamer-cared-text">
            <span>
                Username
            </span>
            <div class="viewer-count">
                <span class="viewer-count-text">0000</span>
                <div class="red-dot"></div>
            </div>
        </div>
    </a>
@else
    <a href="{{ $href }}" class="stream-card" {{ $attributes->merge(["class" => $attributes]) }}>
        <div class="streamer-card-avatar">
            {{$avatar}}
        </div>
        <div class="streamer-cared-text">
            <span>
                {{ $username }}
            </span>
            <div class="viewer-count">
                <span class="viewer-count-text">{{$viewers}}</span>
                <div class="red-dot"></div>
            </div>
        </div>
    </a>
@endif