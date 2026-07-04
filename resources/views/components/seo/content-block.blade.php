@props(['pageKey', 'position' => 'bottom', 'class' => ''])

@php
    use App\Models\PageSeoContent;
    $seoContent = PageSeoContent::forPage($pageKey);

    if ($seoContent && $seoContent->position !== $position) {
        $seoContent = null;
    }
@endphp

@if($seoContent)
<section class="seo-text-block {{ $class }}" aria-label="About this page">
    @if($seoContent->title)
        <h2 class="seo-text-title">{{ $seoContent->title }}</h2>
    @endif

    {{-- Content is always escaped (e()) before nl2br to prevent stored XSS.
         nl2br only injects <br> tags; the text itself is HTML-escaped. --}}
    <div class="seo-text-content">
        {!! nl2br(e($seoContent->content)) !!}
    </div>

    @if($seoContent->keywords_array)
        <div class="seo-text-keywords">
            @foreach($seoContent->keywords_array as $keyword)
                <span class="seo-keyword">{{ $keyword }}</span>
            @endforeach
        </div>
    @endif
</section>
@endif
