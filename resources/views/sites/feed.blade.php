<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $site->name }}</title>
        <link>{{ $site->localizedUrl('/') }}</link>
        <atom:link href="{{ $site->localizedUrl('/feed.xml') }}" rel="self" type="application/rss+xml" />
        <description>{{ config('seo.description') }}</description>
        <language>{{ $site->locale }}</language>
@if($posts->isNotEmpty())
        <lastBuildDate>{{ $posts->first()->updated()?->toRssString() }}</lastBuildDate>
@endif
@foreach($posts as $post)
        <item>
            <title>{{ $post->title() }}</title>
            <link>{{ $site->localizedUrl('/blog/' . $post->slug) }}</link>
            <guid isPermaLink="true">{{ $site->localizedUrl('/blog/' . $post->slug) }}</guid>
@if($post->date())
            <pubDate>{{ $post->date()->toRssString() }}</pubDate>
@endif
            <description>{{ $post->description() }}</description>
@if($post->author())
            <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">{{ $post->author() }}</dc:creator>
@endif
        </item>
@endforeach
    </channel>
</rss>
