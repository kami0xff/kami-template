{{--
    Suggestion panel row. Renders <a> when href is given, else <button>.

    Props:
        href   — link target
        avatar — avatar image URL (mutually exclusive with the icon slot)
        count  — trailing muted counter
    Slots:
        icon     — leading 16px svg
        trailing — custom trailing content (badges, kbd, remove button)
--}}
@props([
    'href' => null,
    'avatar' => null,
    'count' => null,
])

@php $tag = $href ? 'a' : 'button'; @endphp

<{{ $tag }} @if($href) href="{{ $href }}" @else type="button" @endif
    {{ $attributes->merge(['class' => 'search-item']) }} data-search-item>

    @if($avatar)
        <img class="search-item-avatar" src="{{ $avatar }}" alt="" loading="lazy">
    @elseif(isset($icon))
        <span class="search-item-icon">{{ $icon }}</span>
    @endif

    <span class="search-item-label">{{ $slot }}</span>

    @if($count !== null || isset($trailing))
        <span class="search-item-trailing">
            {{ $trailing ?? '' }}
            @if($count !== null){{ number_format($count) }}@endif
        </span>
    @endif
</{{ $tag }}>
