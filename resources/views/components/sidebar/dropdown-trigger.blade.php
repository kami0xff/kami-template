{{--
    Sidebar dropdown trigger — looks like a sidebar link, with a chevron.

    <x-sidebar.dropdown-trigger icon="tag">Categories</x-sidebar.dropdown-trigger>
--}}
@props([
    'icon' => null,
    'disabled' => false,
    'controls' => null,
])
<button
    type="button"
    data-dropdown-trigger
    aria-expanded="false"
    @if($controls) aria-controls="{{ $controls }}" @endif
    {{ $attributes->class([
        'dropdown-trigger',
        'dropdown-trigger--disabled' => $disabled,
    ]) }}
    @if($disabled) disabled @endif
>
    @if($icon)
        <span class="dropdown-trigger-icon">
            <x-sidebar.icon :name="$icon" />
        </span>
    @endif
    <span class="dropdown-trigger-text">{{ $slot }}</span>
    <svg class="dropdown-trigger-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M6 9l6 6 6-6" />
    </svg>
</button>
