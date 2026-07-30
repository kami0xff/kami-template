@props([
    'label',
    'ariaLabel' => null,
])

<nav {{ $attributes->merge([
    'class' => 'sidebar-group',
    'aria-label' => $ariaLabel ?? $label,
]) }}>
    <div class="sidebar-group-title">{{ $label }}</div>
    <div class="sidebar-group-links">
        {{ $slot }}
    </div>
</nav>
