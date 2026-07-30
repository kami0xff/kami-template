{{--
    Explore shell — sticky media/category filters + scrolling feed.

    Props:
        layout: strip | grid
        storage-key: localStorage key for type/category prefs

    Slots:
        types, categories, feed (or default = feed)
--}}
@props([
    'layout' => 'strip',
    'storageKey' => 'kami.explore.filters',
])

<section
    {{ $attributes->class(['explore']) }}
    data-explore
    data-explore-layout="{{ $layout }}"
    data-explore-storage="{{ $storageKey }}"
>
    <div class="explore-bar" data-explore-bar>
        @isset($types)
            {{ $types }}
        @endisset

        @isset($categories)
            {{ $categories }}
        @endisset
    </div>

    @isset($feed)
        {{ $feed }}
    @else
        {{ $slot }}
    @endisset
</section>
