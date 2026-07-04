@extends('site::layout')

@push('seo-pagination')
<x-seo.schema :schemas="$schemas" />
@endpush

@section('site-content')
<x-seo.breadcrumbs :items="$breadcrumbs" />

<article>
    <h1>{{ $post->title() }}</h1>

    <div class="post-meta">
        @if($post->date())
            <time datetime="{{ $post->date()->toDateString() }}">{{ $post->date()->isoFormat('LL') }}</time>
        @endif
        @if($post->author())
            &middot; {{ $post->author() }}
        @endif
        &middot; {{ $post->readingMinutes() }} {{ __('min read') }}
    </div>

    {!! $post->html !!}

    @if($post->tags())
        <div class="post-tags">
            @foreach($post->tags() as $tag)
                <span class="post-tag">{{ $tag }}</span>
            @endforeach
        </div>
    @endif
</article>
@endsection
