@props(['links' => []])

<nav {{ $attributes->merge(['class' => 'sidebar-policy', 'aria-label' => __('footer.legal')]) }}>
    @foreach($links as $link)
        <a
            class="sidebar-policy-link"
            href="{{ $link['href'] ?? '#' }}"
            @if(! empty($link['rel'])) rel="{{ $link['rel'] }}" @endif
            @if(! empty($link['target'])) target="{{ $link['target'] }}" @endif
        >
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>
