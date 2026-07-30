@props([
    'href' => '#',
    'icon' => null,
    'counter' => null,
    'active' => false,
    'variant' => 'regular',
    'rel' => null,
    'target' => null,
])

<a
    href="{{ $href }}"
    @if($rel) rel="{{ $rel }}" @endif
    @if($target) target="{{ $target }}" @endif
    @if($active) aria-current="page" @endif
    {{ $attributes->class([
        'sidebar-link',
        "sidebar-link--{$variant}",
        'sidebar-link--active' => $active,
    ]) }}
>
    @if($icon)
        <span class="sidebar-link-icon">
            @if($icon instanceof \Illuminate\View\ComponentSlot)
                {{ $icon }}
            @else
                <x-sidebar.icon :name="$icon" />
            @endif
        </span>
    @endif

    <span class="sidebar-link-text">{{ $slot }}</span>

    @isset($counter)
        <span class="sidebar-link-counter">{{ is_numeric($counter) ? number_format($counter) : $counter }}</span>
    @endisset
</a>
