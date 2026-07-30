@props([
    'label' => null,
])

<details {{ $attributes->merge(['class' => 'sidebar-language']) }}>
    <summary class="sidebar-language-trigger">
        <x-sidebar.icon name="globe" />
        <span>{{ $label ?? strtoupper(app()->getLocale()) }}</span>
    </summary>
    <div class="sidebar-language-content">
        {{ $slot }}
    </div>
</details>
