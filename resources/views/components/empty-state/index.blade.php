{{--
    Zero-result / first-run placeholder.

    <x-empty-state title="No favorites yet" text="Models you follow show up here.">
        <x-slot:icon><x-icons.heart /></x-slot:icon>
        <x-atom.button.button variant="primary" text="Browse models" />
    </x-empty-state>
--}}
@props([
    'title',
    'text' => null,
])

<div {{ $attributes->class(['empty-state']) }}>
    @isset($icon)
        <span class="empty-state-icon">{{ $icon }}</span>
    @else
        <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
    @endisset

    <h3 class="empty-state-title">{{ $title }}</h3>

    @if($text)
        <p class="empty-state-text">{{ $text }}</p>
    @endif

    @if(trim($slot))
        <div class="empty-state-action">{{ $slot }}</div>
    @endif
</div>
