<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @if(in_array(app()->getLocale(), config('locales.rtl', []))) dir="rtl" @endif>
<head>
    {{-- ===== BASE META ===== --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ===== TITLE ===== --}}
    @php
        $seoTitle = seo()->resolve('title', View::yieldContent('title'));
    @endphp
    <title>{{ $seoTitle ?: config('app.name', 'Kami Template') }}</title>

    {{-- ===== DESCRIPTION ===== --}}
    @php
        $seoDesc = seo()->resolve('description', View::yieldContent('meta_description'));
    @endphp
    @if($seoDesc)
        <meta name="description" content="{{ $seoDesc }}">
    @endif

    {{-- ===== KEYWORDS ===== --}}
    @php
        $seoKeywords = seo()->get('keywords') ?: View::yieldContent('meta_keywords');
    @endphp
    @if($seoKeywords)
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif

    {{-- ===== ROBOTS ===== --}}
    <meta name="robots" content="{{ seo()->get('robots') ?: config('seo.robots', 'index, follow') }}">

    {{-- ===== CANONICAL ===== --}}
    @php
        $canonical = seo()->get('canonical') ?: View::yieldContent('canonical', url()->current());
    @endphp
    <link rel="canonical" href="{{ $canonical }}">

    {{-- ===== OPEN GRAPH ===== --}}
    <meta property="og:type" content="{{ seo()->get('og_type') ?: 'website' }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Kami Template') }}">
    <meta property="og:title" content="{{ $seoTitle ?: config('app.name', 'Kami Template') }}">
    @if($seoDesc)
        <meta property="og:description" content="{{ $seoDesc }}">
    @endif
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">

    @php
        $ogImage = seo()->get('og_image') ?: View::yieldContent('og_image', config('seo.og.image', ''));
    @endphp
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:width" content="{{ seo()->get('og_image_width') ?: 1200 }}">
        <meta property="og:image:height" content="{{ seo()->get('og_image_height') ?: 630 }}">
        <meta property="og:image:alt" content="{{ seo()->get('og_image_alt') ?: config('app.name', 'Kami Template') }}">
    @endif

    {{-- ===== ARTICLE TAGS ===== --}}
    @foreach(seo()->getArticleTags() as $property => $content)
        @if(is_array($content))
            @foreach($content as $tagValue)
                <meta property="{{ $property }}" content="{{ $tagValue }}">
            @endforeach
        @else
            <meta property="{{ $property }}" content="{{ $content }}">
        @endif
    @endforeach

    {{-- ===== TWITTER CARD ===== --}}
    <meta name="twitter:card" content="{{ seo()->get('twitter_card') ?: 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ seo()->get('twitter_title') ?: $seoTitle ?: config('app.name', 'Kami Template') }}">
    @if($seoDesc)
        <meta name="twitter:description" content="{{ seo()->get('twitter_description') ?: $seoDesc }}">
    @endif
    @php
        $twitterImage = seo()->get('twitter_image') ?: $ogImage;
    @endphp
    @if($twitterImage)
        <meta name="twitter:image" content="{{ $twitterImage }}">
    @endif
    @php
        $twitterSite = seo()->get('twitter_site') ?: config('seo.twitter.site');
    @endphp
    @if($twitterSite)
        <meta name="twitter:site" content="{{ $twitterSite }}">
    @endif
    @php
        $twitterCreator = seo()->get('twitter_creator') ?: config('seo.twitter.creator');
    @endphp
    @if($twitterCreator)
        <meta name="twitter:creator" content="{{ $twitterCreator }}">
    @endif

    {{-- ===== THEME COLOR ===== --}}
    <meta name="theme-color" content="{{ config('seo.theme_color', '#000000') }}">

    {{-- ===== SEO STACKS ===== --}}
    @stack('seo-head')
    @stack('seo-pagination')

    {{-- ===== FAVICON ===== --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- ===== RAW TAGS ===== --}}
    @foreach(seo()->getRawTags() as $rawTag)
        {!! $rawTag !!}
    @endforeach

    {{-- ===== CUSTOM META TAGS ===== --}}
    @foreach(seo()->getTags() as $property => $content)
        <meta property="{{ $property }}" content="{{ $content }}">
    @endforeach

    {{-- ===== RSS FEED ===== --}}
    @if(config('seo.feed.enabled'))
        <link rel="alternate"
              type="{{ config('seo.feed.type', 'application/rss+xml') }}"
              title="{{ config('seo.feed.title', config('app.name')) }}"
              href="{{ url(config('seo.feed.url', '/feed')) }}">
    @endif

    {{-- ===== STYLES ===== --}}
    @stack('styles')
    @stack('head')

    {{-- ===== GOOGLE ANALYTICS ===== --}}
    @if(config('services.google.analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google.analytics_id') }}');
        </script>
    @endif

    {{-- ===== OPENREPLAY SESSION RECORDING ===== --}}
    @production
    @if(config('services.openreplay.project_key'))
    <script>
      var initOpts = {
        projectKey: {!! json_encode(config('services.openreplay.project_key')) !!},
        defaultInputMode: 0,
        obscureTextNumbers: false,
        obscureTextEmails: false,
      };
      var startOpts = { userID: {!! json_encode(auth()->check() ? (string) auth()->user()->id : '') !!} };
      (function(A,s,a,y,e,r){
        r=window.OpenReplay=[e,r,y,[s-1, e]];
        s=document.createElement('script');s.src=A;s.async=!a;
        document.getElementsByTagName('head')[0].appendChild(s);
        r.start=function(v){r.push([0])};
        r.stop=function(v){r.push([1])};
        r.setUserID=function(id){r.push([2,id])};
        r.setUserAnonymousID=function(id){r.push([3,id])};
        r.setMetadata=function(k,v){r.push([4,k,v])};
        r.event=function(k,p,i){r.push([5,k,p,i])};
        r.issue=function(k,p){r.push([6,k,p])};
        r.isActive=function(){return false};
        r.getSessionToken=function(){};
      })("//static.openreplay.com/latest/openreplay.js",1,0,initOpts,startOpts);
      @auth
      window.OpenReplay.setUserID({!! json_encode(auth()->user()->email) !!});
      window.OpenReplay.setMetadata('name', {!! json_encode(auth()->user()->name) !!});
      @endauth
    </script>
    @endif
    @endproduction
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
