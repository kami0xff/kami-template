{{--
    Soft paywall / media lock overlay for thumbnails & cards.

    <x-thumbnail src="…" ratio="portrait">
        <x-media.lock price="120" label="Unlock gallery" />
    </x-thumbnail>
--}}
@props([
    'price' => null,
    'currency' => 'tokens',
    'label' => 'Unlock',
    'blur' => true,
    'href' => null,
])

<div {{ $attributes->class(['media-lock', 'media-lock--blur' => $blur]) }} data-media-lock>
    <div class="media-lock-veil" aria-hidden="true"></div>
    <div class="media-lock-content">
        <span class="media-lock-icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>
            </svg>
        </span>
        <span class="media-lock-label">{{ $label }}</span>
        @if($price !== null)
            <span class="media-lock-price">{{ $price }} {{ $currency }}</span>
        @endif
        @if($href)
            <a class="media-lock-cta" href="{{ $href }}">{{ $label }}</a>
        @else
            <button type="button" class="media-lock-cta" data-media-unlock>{{ $label }}</button>
        @endif
    </div>
</div>
