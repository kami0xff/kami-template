{{--
    A directory entry. The link text is what the search box and the A–Z bar
    match (its first character picks the letter; digits and symbols go under #).

    Props:
        href
        count      — muted number after the name (pre-formatted); null hides it
        countLabel — tooltip for the number ("Live now", "Posts")
        active     — current page
    Slots: icon (before the name), default (the name)
--}}
@props([
    'href',
    'count' => null,
    'countLabel' => null,
    'active' => false,
])

<li {{ $attributes->class(['directory-item']) }} data-directory-item>
    <a href="{{ $href }}" class="directory-link" @if ($active) aria-current="page" @endif>
        @isset($icon)<span class="directory-item-icon" aria-hidden="true">{{ $icon }}</span>@endisset{{ $slot }}
    </a>
    @if ($count !== null && $count !== '')
        <span class="directory-count" @if ($countLabel) title="{{ $countLabel }}" @endif>{{ $count }}</span>
    @endif
</li>
