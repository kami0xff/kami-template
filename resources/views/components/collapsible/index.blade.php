{{--
    Simple disclosure / accordion item with animated chevron.

    Usage:
        <x-collapsible title="What is this?">Answer…</x-collapsible>

        <x-collapsible title="Billing" meta="Updated yesterday" variant="panel" :open="true">
            …
        </x-collapsible>

    Props:
        title    — summary label
        meta     — optional secondary line under the title
        open     — start expanded
        variant  — default | panel | ghost
--}}
@props([
    'title',
    'meta' => null,
    'open' => false,
    'variant' => 'default',
])

<details
    {{ $attributes->class([
        'collapsible',
        'collapsible--' . $variant => $variant && $variant !== 'default',
    ]) }}
    data-collapsible
    @if($open) open @endif
>
    <summary class="collapsible-summary">
        <span class="collapsible-title">
            <span class="collapsible-title-text">{{ $title }}</span>
            @if($meta)
                <span class="collapsible-title-meta">{{ $meta }}</span>
            @endif
        </span>
        <svg class="collapsible-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M6 9l6 6 6-6" />
        </svg>
    </summary>
    <div class="collapsible-content">
        {{ $slot }}
    </div>
</details>
