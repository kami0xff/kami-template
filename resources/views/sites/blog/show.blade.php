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
        @if($post->updated() && $post->date() && !$post->updated()->isSameDay($post->date()))
            &middot; {{ __('Updated') }} <time datetime="{{ $post->updated()->toDateString() }}">{{ $post->updated()->isoFormat('LL') }}</time>
        @endif
        @if($authorName = $post->author() ?? ($site->author['name'] ?? null))
            &middot;
            @if($bySiteAuthor && !empty($site->author['url']))
                <a href="{{ $site->author['url'] }}" rel="author">{{ $authorName }}</a>
            @else
                <span rel="author">{{ $authorName }}</span>
            @endif
        @endif
        &middot; {{ $post->readingMinutes() }} {{ __('min read') }}
    </div>

    @if($cluster && $cluster['role'] === 'spoke')
        <p class="cluster-hub">
            {{ __('Part of our guide:') }}
            <a href="{{ $site->localizedUrl('/blog/' . $cluster['pillar']->slug) }}">{{ $cluster['pillar']->title() }}</a>
        </p>
    @endif

    @if($post->tldr())
        <aside class="tldr" aria-label="{{ __('Summary') }}">
            <strong>{{ __('TL;DR') }}</strong>
            <ul>
                @foreach($post->tldr() as $point)
                    <li>{{ $point }}</li>
                @endforeach
            </ul>
        </aside>
    @endif

    {!! $post->html !!}

    @if($cluster && $cluster['role'] === 'pillar')
        <section class="cluster-spokes">
            <h2>{{ __('In this guide') }}</h2>
            <ul class="post-list">
                @foreach($cluster['spokes'] as $spoke)
                    <li class="post-list-item">
                        <h3><a href="{{ $site->localizedUrl('/blog/' . $spoke->slug) }}">{{ $spoke->title() }}</a></h3>
                        <p>{{ $spoke->excerpt(120) }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($post->quiz())
        @include('site::partials.quiz', ['quiz' => $post->quiz()])
    @endif

    @if($post->faq())
        <section class="post-faq">
            <h2>{{ __('Frequently asked questions') }}</h2>
            @foreach($post->faq() as $item)
                <details>
                    <summary>{{ $item['question'] }}</summary>
                    <p>{{ $item['answer'] }}</p>
                </details>
            @endforeach
        </section>
    @endif

    @if($post->sources())
        <section class="post-sources">
            <h2>{{ __('References') }}</h2>
            <ol>
                @foreach($post->sources() as $source)
                    <li><a href="{{ $source['url'] }}" rel="nofollow noopener" target="_blank">{{ $source['title'] ?? $source['url'] }}</a></li>
                @endforeach
            </ol>
        </section>
    @endif

    @if($post->tags())
        <div class="post-tags">
            @foreach($post->tags() as $tag)
                <span class="post-tag">{{ $tag }}</span>
            @endforeach
        </div>
    @endif
</article>

@if(!empty($site->newsletter['enabled']))
    @include('site::partials.lead-form', [
        'form' => 'newsletter',
        'heading' => $site->newsletter['heading'] ?? __('Get new articles by email'),
        'text' => $site->newsletter['text'] ?? null,
        'button' => $site->newsletter['button'] ?? __('Subscribe'),
    ])
@endif

@if($bySiteAuthor)
    <aside class="author-box">
        @if(!empty($site->author['avatar']))
            <img src="{{ $site->author['avatar'] }}" alt="{{ $site->author['name'] }}" class="author-avatar" width="64" height="64" loading="lazy">
        @endif
        <div>
            <strong>
                @if(!empty($site->author['url']))
                    <a href="{{ $site->author['url'] }}" rel="author">{{ $site->author['name'] }}</a>
                @else
                    {{ $site->author['name'] }}
                @endif
            </strong>
            @if(!empty($site->author['title']))
                <span class="author-title">{{ $site->author['title'] }}</span>
            @endif
            @if(!empty($site->author['bio']))
                <p>{{ $site->author['bio'] }}</p>
            @endif
            @if(!empty($site->author['same_as']))
                <nav class="author-links" aria-label="{{ __('Author profiles') }}">
                    @foreach($site->author['same_as'] as $profile)
                        <a href="{{ $profile }}" rel="me noopener" target="_blank">{{ parse_url($profile, PHP_URL_HOST) }}</a>
                    @endforeach
                </nav>
            @endif
        </div>
    </aside>
@endif

@if($related->isNotEmpty())
    <section class="related-posts">
        <h2>{{ __('Related articles') }}</h2>
        <ul class="post-list">
            @foreach($related as $rel)
                <li class="post-list-item">
                    <h3><a href="{{ $site->localizedUrl('/blog/' . $rel->slug) }}">{{ $rel->title() }}</a></h3>
                    <p>{{ $rel->excerpt(120) }}</p>
                </li>
            @endforeach
        </ul>
    </section>
@endif
@endsection
