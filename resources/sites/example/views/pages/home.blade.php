{{--
    A Blade home page (wins over content/pages/home.md if both exist).
    Use Blade when a page needs layout/logic; use markdown for plain content.
    $posts (published blog posts, newest first) is available on all Blade pages.
--}}
@extends('site::layout')

@php
    seo()->title('Example Site — Static Multi-Site Demo')
        ->description('An example static site demonstrating markdown content, blog posts, and per-site SEO.')
        ->canonical($site->url('/'));
@endphp

@push('seo-pagination')
<x-seo.schema :schemas="[
    app(\App\Services\SeoService::class)->getHomepageSchema($site->name),
    app(\App\Services\SeoService::class)->getOrganizationSchema(),
]" />
@endpush

@section('site-content')
<h1>{{ $site->name }}</h1>

<p>
    This site lives in <code>resources/sites/example/</code> and is served by its
    domain ({{ $site->canonicalDomain() }}) — one Laravel project, many static sites.
</p>

<h2>{{ __('Latest articles') }}</h2>

<ul class="post-list">
    @foreach($posts->take(5) as $post)
        <li class="post-list-item">
            <h2><a href="{{ $site->url('/blog/' . $post->slug) }}">{{ $post->title() }}</a></h2>
            <p>{{ $post->excerpt(140) }}</p>
        </li>
    @endforeach
</ul>

<p><a href="{{ $site->url('/blog') }}">{{ __('All articles') }} &rarr;</a></p>
@endsection
