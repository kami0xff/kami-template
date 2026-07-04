@extends('site::layout')

@push('seo-pagination')
<x-seo.schema :schemas="$schemas" />
@endpush

@section('site-content')
<h1>{{ __('Blog') }}</h1>

@if($posts->isEmpty())
    <p>{{ __('No articles yet.') }}</p>
@else
    <ul class="post-list">
        @foreach($posts as $post)
            <li class="post-list-item">
                <h2><a href="{{ $site->localizedUrl('/blog/' . $post->slug) }}">{{ $post->title() }}</a></h2>
                @if($post->date())
                    <time datetime="{{ $post->date()->toDateString() }}" class="post-meta">
                        {{ $post->date()->isoFormat('LL') }}
                    </time>
                @endif
                <p>{{ $post->excerpt(180) }}</p>
            </li>
        @endforeach
    </ul>
@endif
@endsection
