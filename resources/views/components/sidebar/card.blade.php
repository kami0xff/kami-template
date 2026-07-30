@props([
    'href' => null,
])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'sidebar-card']) }}>
        @isset($media)
            <span class="sidebar-card-media">{{ $media }}</span>
        @endisset

        <span class="sidebar-card-body">{{ $slot }}</span>
    </a>
@else
    <div {{ $attributes->merge(['class' => 'sidebar-card']) }}>
        @isset($media)
            <div class="sidebar-card-media">{{ $media }}</div>
        @endisset

        <div class="sidebar-card-body">{{ $slot }}</div>
    </div>
@endif
