{{--
    Action pill inside the search bar ("✦ Magic Search 3").

    Props:
        count — optional counter bubble
--}}
@props(['count' => null])

<button type="button" {{ $attributes->merge(['class' => 'search-chip']) }}>
    <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2l2.1 6.3L20 10l-5.9 1.7L12 18l-2.1-6.3L4 10l5.9-1.7L12 2z" />
    </svg>
    {{ $slot }}
    @if($count !== null)
        <span class="search-chip-count">{{ $count }}</span>
    @endif
</button>
