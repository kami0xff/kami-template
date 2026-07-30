{{--
    Fixed-ratio media frame with overlay corner slots.

    <x-thumbnail src="/img/preview.jpg" ratio="photo">
        <x-slot:overlays>
            <span class="thumbnail-overlay thumbnail-overlay--tl"><x-atom.status-dot status="live" /> LIVE</span>
            <span class="thumbnail-overlay thumbnail-overlay--br">12:31</span>
        </x-slot:overlays>
    </x-thumbnail>
--}}
@props([
    'src' => null,
    'alt' => '',
    'ratio' => null, // square | portrait | photo (default 16/9)
])

<div {{ $attributes->class(['thumbnail', "thumbnail--$ratio" => $ratio]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy">
    @endif
    {{ $slot }}
    {{ $overlays ?? '' }}
</div>
