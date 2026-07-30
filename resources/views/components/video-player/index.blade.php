{{--
    Custom HTML5 video player — dark floating chrome.

    <x-video-player
        src="https://…/clip.mp4"
        poster="https://…/poster.jpg"
        title="Studio recap"
    />

    Locked / paywalled:
        <x-video-player src="…" poster="…">
            <x-media.lock price="80" label="Unlock video" />
        </x-video-player>

    Live badge:
        <x-video-player src="…" :live="true" />
--}}
@props([
    'src' => null,
    'sources' => [], // [['src' => '…', 'type' => 'video/mp4'], …]
    'poster' => null,
    'title' => null,
    'autoplay' => false,
    'muted' => false,
    'loop' => false,
    'playsinline' => true,
    'controls' => true,
    'live' => false,
    'ratio' => 'landscape', // landscape | cinema | square | portrait
    'preload' => 'metadata',
])

@php
    $ratioClass = match ($ratio) {
        'cinema' => 'video-player--cinema',
        'square' => 'video-player--square',
        'portrait' => 'video-player--portrait',
        default => null,
    };
    $sourceList = $sources;
    if ($src) {
        array_unshift($sourceList, ['src' => $src, 'type' => null]);
    }
@endphp

<div
    {{ $attributes->class(['video-player', 'is-idle', 'is-paused', $ratioClass]) }}
    data-video-player
    @if($title) data-title="{{ $title }}" @endif
    tabindex="0"
    role="region"
    aria-label="{{ $title ?: 'Video player' }}"
>
    <video
        class="video-player-media"
        data-vp-media
        preload="{{ $preload }}"
        @if($poster) poster="{{ $poster }}" @endif
        @if($autoplay) autoplay @endif
        @if($muted) muted @endif
        @if($loop) loop @endif
        @if($playsinline) playsinline @endif
    >
        @foreach($sourceList as $source)
            @if(is_array($source))
                <source src="{{ $source['src'] }}" @if(!empty($source['type'])) type="{{ $source['type'] }}" @endif>
            @else
                <source src="{{ $source }}">
            @endif
        @endforeach
    </video>

    @if($poster)
        <img class="video-player-poster" src="{{ $poster }}" alt="" data-vp-poster>
    @endif

    @if($live)
        <span class="video-player-badge video-player-badge--live" data-vp-live>
            <span class="video-player-badge-dot" aria-hidden="true"></span>
            Live
        </span>
    @endif

    @if($slot->isNotEmpty())
        <div class="video-player-overlay">
            {{ $slot }}
        </div>
    @endif

    <button type="button" class="video-player-bigplay" data-vp-bigplay aria-label="Play">
        <span class="video-player-bigplay-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M8.2 5.6v12.8c0 .7.8 1.1 1.4.7l10-6.4a.8.8 0 0 0 0-1.4l-10-6.4a.8.8 0 0 0-1.4.7z"/></svg>
        </span>
    </button>

    @if($controls)
        <div class="video-player-controls" data-vp-controls>
            <div class="video-player-progress" data-vp-progress>
                <div class="video-player-progress-track">
                    <div class="video-player-progress-buffer" data-vp-buffer></div>
                    <div class="video-player-progress-played" data-vp-played></div>
                </div>
                <input
                    class="video-player-progress-input"
                    type="range"
                    min="0"
                    max="1000"
                    value="0"
                    step="1"
                    data-vp-seek
                    aria-label="Seek"
                >
            </div>

            <div class="video-player-bar">
                <button type="button" class="video-player-btn" data-vp-play aria-label="Play">
                    <svg data-vp-icon-play viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.2 5.6v12.8c0 .7.8 1.1 1.4.7l10-6.4a.8.8 0 0 0 0-1.4l-10-6.4a.8.8 0 0 0-1.4.7z"/></svg>
                    <svg data-vp-icon-pause viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" hidden><path d="M7 5.5h3.2v13H7zm6.8 0H17v13h-3.2z"/></svg>
                </button>

                <div class="video-player-volume">
                    <button type="button" class="video-player-btn" data-vp-mute aria-label="Mute">
                        <svg data-vp-icon-vol viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10v4h3.2L12 18V6L7.2 10H4z"/><path d="M15.2 9.2a3.4 3.4 0 0 1 0 5.6"/><path d="M17.4 7a6 6 0 0 1 0 10"/></svg>
                        <svg data-vp-icon-muted viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" hidden><path d="M4 10v4h3.2L12 18V6L7.2 10H4z"/><path d="m16 9 5 5M21 9l-5 5"/></svg>
                    </button>
                    <input class="video-player-volume-range" type="range" min="0" max="1" step="0.05" value="1" data-vp-volume aria-label="Volume">
                </div>

                <div class="video-player-time" data-vp-time>
                    <strong data-vp-current>0:00</strong>
                    <span aria-hidden="true">/</span>
                    <span data-vp-duration>{{ $live ? 'Live' : '0:00' }}</span>
                </div>

                <div class="video-player-bar-end">
                    <div class="video-player-menu" data-vp-rate-menu>
                        <button type="button" class="video-player-btn" data-vp-rate-toggle aria-label="Playback speed" aria-haspopup="true" aria-expanded="false">
                            <span data-vp-rate-label style="font-size:11px; font-weight:700;">1×</span>
                        </button>
                        <div class="video-player-menu-panel" data-vp-rate-panel hidden>
                            @foreach([0.5, 0.75, 1, 1.25, 1.5, 2] as $rate)
                                <button type="button" class="video-player-menu-item {{ $rate === 1.0 ? 'is-active' : '' }}" data-vp-rate="{{ $rate }}">{{ $rate }}×</button>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="video-player-btn" data-vp-pip aria-label="Picture in picture">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><rect x="12" y="11" width="7" height="5" rx="1"/></svg>
                    </button>

                    <button type="button" class="video-player-btn" data-vp-fs aria-label="Fullscreen">
                        <svg data-vp-icon-fs viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 4H4v4M16 4h4v4M8 20H4v-4M16 20h4v-4"/></svg>
                        <svg data-vp-icon-fs-exit viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" hidden><path d="M4 9h5V4M20 9h-5V4M4 15h5v5M20 15h-5v5"/></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
