{{--
    Scroll-snap carousel. Slides are direct children of the default slot.

    <x-carousel :dots="true" full>
        <img src="/img/1.jpg" alt="">
        <img src="/img/2.jpg" alt="">
    </x-carousel>

    `full` makes each slide 100% wide; omit it for a multi-item strip
    (give slides their own widths).
--}}
@props([
    'arrows' => true,
    'dots' => false,
    'full' => false,
])

<div {{ $attributes->class(['carousel', 'carousel--full' => $full]) }} data-carousel>
    @if($arrows)
        <button type="button" class="carousel-arrow carousel-arrow--prev" data-carousel-prev aria-label="Previous slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
        </button>
    @endif

    <div class="carousel-track">
        {{ $slot }}
    </div>

    @if($arrows)
        <button type="button" class="carousel-arrow carousel-arrow--next" data-carousel-next aria-label="Next slide">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
        </button>
    @endif

    @if($dots)
        <div class="carousel-dots" data-carousel-dots></div>
    @endif
</div>
