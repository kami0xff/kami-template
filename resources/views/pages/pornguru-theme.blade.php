@extends('layouts.app')

@section('title', 'Pornguru — Theme demo')

@push('styles')
    {{-- Warm charcoal / coral theme — token overrides only, loaded after app.css --}}
    <link rel="stylesheet" href="{{ vite_asset('assets/pornguru-theme.css') }}">
@endpush

@section('content')
<x-app-shell brand="Pornguru">
    <x-slot:subnav>
        <a href="#" class="subnavbar-link active">Models</a>
        <a href="#" class="subnavbar-link">Studios</a>
        <a href="#" class="subnavbar-link">Categories</a>
        <a href="#" class="subnavbar-link">Live</a>
    </x-slot:subnav>

    <x-slot:sidebar>
        <x-sidebar.sidebar>
            <x-sidebar.group label="Menu">
                <x-sidebar.link href="#" icon="home" :active="true">Home</x-sidebar.link>
                <x-sidebar.link href="#" icon="feed">Feed</x-sidebar.link>
                <x-sidebar.link href="#" icon="star">Recommended</x-sidebar.link>
                <x-sidebar.link href="#" icon="heart">My Favorites</x-sidebar.link>
                <x-sidebar.link href="#" icon="bookmark">My Collection</x-sidebar.link>
            </x-sidebar.group>

            <x-sidebar.group label="Browse">
                <x-sidebar.link href="#" :counter="1832">Amateur</x-sidebar.link>
                <x-sidebar.link href="#" :counter="3991">MILF</x-sidebar.link>
                <x-sidebar.link href="#" :counter="1199">Anal</x-sidebar.link>
                <x-sidebar.link href="#" :counter="640">Lesbian</x-sidebar.link>
            </x-sidebar.group>

            <x-atom.pill variant="outline" style="width:100%; justify-content:center;">All categories</x-atom.pill>
        </x-sidebar.sidebar>
    </x-slot:sidebar>

    <div class="container page-section">
        <p style="color:var(--text-muted); font-size:var(--text-small); margin-bottom:var(--space5);">
            Theme demo — coral <code>--main1</code> on warm charcoal neutrals. Same components as
            <a href="{{ route('hhentai') }}" style="color:var(--color-primary);">/hhentai</a>, different tokens only.
        </p>
        @foreach([
            ['title' => 'Trending now', 'seeds' => [401, 402, 403, 404, 405], 'live' => [0, 3]],
            ['title' => 'New this week', 'seeds' => [501, 502, 503, 504, 505], 'live' => [1]],
            ['title' => 'Because you watched…', 'seeds' => [601, 602, 603, 604, 605], 'live' => [2, 4]],
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
