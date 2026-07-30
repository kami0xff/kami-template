{{--
    Drill-in trigger — a sidebar row that opens a <x-sidebar.sub-sidebar>.
    `target` must match the sub-sidebar's `id`.

    <x-sidebar.sub-trigger target="categories" icon="tag" :counter="1200">
        All Categories
    </x-sidebar.sub-trigger>
--}}
@props([
    'target',
    'icon' => null,
    'counter' => null,
])

<button
    type="button"
    data-action="sub-sidebar"
    data-target="{{ $target }}"
    aria-expanded="false"
    aria-controls="sub-sidebar-{{ $target }}"
    {{ $attributes->class(['sidebar-link', 'sidebar-link--regular', 'sidebar-link--sub']) }}
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

    <span class="sidebar-link-trailing">
        @isset($counter)
            <span class="sidebar-link-counter">{{ is_numeric($counter) ? number_format($counter) : $counter }}</span>
        @endisset
        <svg class="sidebar-link-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 18l6-6-6-6" />
        </svg>
    </span>
</button>
