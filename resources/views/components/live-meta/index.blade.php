{{--
    Live stream meta strip — viewers · duration · quality.

    <x-live-meta :viewers="1842" duration="01:12:04" quality="HD" />
--}}
@props([
    'viewers' => null,
    'duration' => null,
    'quality' => null,
    'live' => true,
])

<div {{ $attributes->class(['live-meta']) }} data-live-meta>
    @if($live)
        <span class="live-meta-live"><span class="status-dot status-dot--live" aria-hidden="true"></span> LIVE</span>
    @endif
    @if($viewers !== null)
        <span class="live-meta-item" title="Viewers">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ is_numeric($viewers) ? number_format((int) $viewers) : $viewers }}
        </span>
    @endif
    @if($duration)
        <span class="live-meta-item" title="Duration">{{ $duration }}</span>
    @endif
    @if($quality)
        <span class="live-meta-quality">{{ $quality }}</span>
    @endif
    {{ $slot }}
</div>
