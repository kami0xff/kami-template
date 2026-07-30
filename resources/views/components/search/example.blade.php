{{--
    Gradient-bordered Magic Search example chip.

    Props:
        href — optional link target (renders <a>, else <button>)
--}}
@props(['href' => null])

@php $tag = $href ? 'a' : 'button'; @endphp

<{{ $tag }} @if($href) href="{{ $href }}" @else type="button" @endif
    {{ $attributes->merge(['class' => 'search-example']) }} data-search-item>

    <span class="search-example-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l1.6 5L19 8.4l-5.4 1.4L12 15l-1.6-5.2L5 8.4l5.4-1.4L12 2z" />
            <path d="M19 13l.8 2.4L22 16l-2.2.6L19 19l-.8-2.4L16 16l2.2-.6L19 13z" />
        </svg>
    </span>
    <span class="search-example-label">{{ $slot }}</span>
</{{ $tag }}>
