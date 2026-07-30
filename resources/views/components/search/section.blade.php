{{--
    Suggestion panel section.

    Props:
        title — optional uppercase section title
    Slots:
        action — optional right-aligned control ("Clear", "See all")
--}}
@props(['title' => null])

<div {{ $attributes->merge(['class' => 'search-section']) }}>
    @if($title || isset($action))
        <div class="search-section-head">
            <span class="search-section-title">{{ $title }}</span>
            {{ $action ?? '' }}
        </div>
    @endif
    {{ $slot }}
</div>
