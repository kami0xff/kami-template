@extends('layouts.app')

@section('title', 'HHentai — Theme demo')

@push('styles')
    {{-- Navy/azure theme — token overrides only, loaded after app.css --}}
    <link rel="stylesheet" href="{{ vite_asset('assets/hhentai-theme.css') }}">
@endpush

@section('content')
<x-app-shell brand="HHentai">
    <x-slot:subnav>
        <a href="#" class="subnavbar-link active">Filles</a>
        <a href="#" class="subnavbar-link">Couples</a>
        <a href="#" class="subnavbar-link">Hommes</a>
        <a href="#" class="subnavbar-link">Trans</a>
    </x-slot:subnav>

    <x-slot:sidebar>
        <x-sidebar.sidebar>
            <x-sidebar.group label="Menu">
                <x-sidebar.link href="#" icon="home" :active="true">Home</x-sidebar.link>
                <x-sidebar.link href="#" icon="feed">Feed</x-sidebar.link>
                <x-sidebar.link href="#" icon="star">Recommended</x-sidebar.link>
                <x-sidebar.link href="#" icon="heart">My Favorites</x-sidebar.link>
                <x-sidebar.link href="#" icon="monitor">Top Private Shows</x-sidebar.link>
                <x-sidebar.link href="#" icon="bookmark">My Collection</x-sidebar.link>
                <x-sidebar.link href="#" icon="clock">Viewed Shows</x-sidebar.link>
            </x-sidebar.group>

            <x-sidebar.group label="Specialties">
                <x-sidebar.link href="#" :counter="64"><x-atom.flag code="fr" /> French</x-sidebar.link>
                <x-sidebar.link href="#" :counter="141"><x-atom.flag code="ua" /> Ukrainian</x-sidebar.link>
                <x-sidebar.link href="#" :counter="1343">New Models</x-sidebar.link>
                <x-sidebar.link href="#" :counter="161">VR Cams</x-sidebar.link>
                <x-sidebar.link href="#" :counter="78">BDSM</x-sidebar.link>
                <x-sidebar.link href="#" :counter="52">Ticket Shows</x-sidebar.link>
            </x-sidebar.group>

            <x-sidebar.group label="For you">
                <x-sidebar.link href="#" :counter="3376">Big Tits</x-sidebar.link>
            </x-sidebar.group>

            <x-atom.pill variant="outline" style="width:100%; justify-content:center;">Categories +</x-atom.pill>
        </x-sidebar.sidebar>
    </x-slot:sidebar>

    <div class="container page-section">
        @foreach([
            ['title' => 'My favorites', 'seeds' => [101, 102, 103, 104, 105], 'live' => [0, 2]],
            ['title' => 'Our daily recommendations for you', 'seeds' => [201, 202, 203, 204, 205], 'live' => [1]],
            ['title' => 'These shows might interest you', 'seeds' => [301, 302, 303, 304, 305], 'live' => [0, 3]],
        ] as $section)
            <section style="margin-bottom:var(--space6);">
                <div style="display:flex; align-items:baseline; justify-content:space-between; gap:var(--space3); margin-bottom:var(--space3);">
                    <h2 style="color:var(--color-primary); font-size:var(--text-subheading);">{{ $section['title'] }}</h2>
                    <x-atom.pill size="sm" href="#">See all</x-atom.pill>
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(190px, 1fr)); gap:var(--space3);">
                    @foreach($section['seeds'] as $i => $seed)
                        <a href="https://picsum.photos/seed/{{ $seed }}/1200/675" data-lightbox="{{ Str::slug($section['title']) }}">
                            <x-thumbnail src="https://picsum.photos/seed/{{ $seed }}/380/214">
                                <x-slot:overlays>
                                    @if(in_array($i, $section['live']))
                                        <span class="thumbnail-overlay thumbnail-overlay--tl"><x-atom.status-dot status="live" /> LIVE</span>
                                    @endif
                                </x-slot:overlays>
                            </x-thumbnail>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-app-shell>
@endsection
