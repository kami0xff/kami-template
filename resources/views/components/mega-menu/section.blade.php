{{--
    Full-width section band inside a mega menu.

    Props:
        title, actionHref, actionLabel
--}}
@props([
    'title' => null,
    'actionHref' => null,
    'actionLabel' => 'See all',
])

<section {{ $attributes->class(['mega-menu-section']) }}>
    @if($title || $actionHref)
        <div class="mega-menu-section-head">
            @if($title)
                <h3 class="mega-menu-section-title">{{ $title }}</h3>
            @endif
            @if($actionHref)
                <a class="mega-menu-section-action" href="{{ $actionHref }}">{{ $actionLabel }}</a>
            @endif
        </div>
    @endif
    {{ $slot }}
</section>
