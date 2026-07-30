@php
    /**
     * Simple brand glyphs for creator social chips.
     * Keys match the `network` prop on <x-creator.social>.
     */
    $icons = [
        'x' => '<path d="M18.2 3H21l-6.6 7.5L22 21h-6.2l-4.3-5.5L6.2 21H3.4l7-8L2 3h6.3l3.9 5.1L18.2 3zm-1.1 16.2h1.7L7 4.7H5.2l11.9 14.5z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="17.5" cy="6.5" r="1.2"/>',
        'tiktok' => '<path d="M14 4c.4 2.4 1.8 4 4 4.4V11c-1.5-.1-2.9-.6-4-1.4V15a5 5 0 1 1-5-5c.3 0 .7 0 1 .1V13a2.5 2.5 0 1 0 2.5 2.5V4h1.5z"/>',
        'youtube' => '<path d="M22 12s0-3.4-.4-5c-.3-1.2-1.2-2-2.4-2.3C17.4 4.2 12 4.2 12 4.2s-5.4 0-7.2.5C3.6 5 2.7 5.8 2.4 7 2 8.6 2 12 2 12s0 3.4.4 5c.3 1.2 1.2 2 2.4 2.3 1.8.5 7.2.5 7.2.5s5.4 0 7.2-.5c1.2-.3 2.1-1.1 2.4-2.3.4-1.6.4-5 .4-5z"/><path d="M10 15.5v-7l6 3.5-6 3.5z" fill="var(--creator-section-bg, #2a2a2a)"/>',
        'discord' => '<path d="M8.5 9.2c-.7 0-1.2.6-1.2 1.2 0 .7.5 1.3 1.2 1.3s1.2-.6 1.2-1.3c0-.6-.5-1.2-1.2-1.2zm7 0c-.7 0-1.2.6-1.2 1.2 0 .7.5 1.3 1.2 1.3s1.2-.6 1.2-1.3c0-.6-.5-1.2-1.2-1.2z"/><path d="M19.3 5.4A16 16 0 0 0 15.2 4l-.3.6a14 14 0 0 1 3.3 1.2 12 12 0 0 0-10.4 0A14 14 0 0 1 9 4.6L8.8 4a16 16 0 0 0-4.1 1.4C2.6 8.5 2 11.7 2.2 14.8c1.5 1.1 3 1.8 4.5 2.2l.7-1.1a8.5 8.5 0 0 1-1.5-.7l.4-.3c3 1.4 6.3 1.4 9.3 0l.4.3c-.5.3-1 .5-1.5.7l.7 1.1c1.5-.4 3-1.1 4.5-2.2.3-3.6-.5-6.7-2-9.4z"/>',
        'facebook' => '<path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H7v3h3v7h3v-7h3l1-3h-4v-2c0-.6.4-1 1-1z"/>',
        'telegram' => '<path d="M21.5 4.3 3.7 11.2c-1.2.5-1.2 1.1-.2 1.4l4.6 1.4 1.8 5.5c.2.7.6.8 1.2.5l2.7-2.2 5.3 3.9c1 .5 1.7.3 2-.9L22.9 5.5c.4-1.4-.5-2-1.4-1.2z"/>',
        'link' => '<path d="M10.5 13.5a4 4 0 0 0 5.7 0l2.1-2.1a4 4 0 0 0-5.7-5.7l-1.2 1.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M13.5 10.5a4 4 0 0 0-5.7 0L5.7 12.6a4 4 0 1 0 5.7 5.7l1.2-1.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
    ];
@endphp

@props([
    'network' => 'link',
    'href' => '#',
    'label' => null,
])

@php
    $network = strtolower($network);
    $path = $icons[$network] ?? $icons['link'];
    $label = $label ?? ucfirst($network);
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->class(['creator-social']) }}
    target="_blank"
    rel="noopener noreferrer"
    title="{{ $label }}"
>
    <span class="creator-social-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">{!! $path !!}</svg>
    </span>
    <span class="creator-social-label">{{ $label }}</span>
</a>
