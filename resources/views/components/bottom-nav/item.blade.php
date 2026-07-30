@props([
    'href' => '#',
    'label' => '',
    'icon' => 'home', // home | feed | heart | clock | chat | compass | wallet | user | plus
    'active' => false,
    'badge' => null,
    'badgeDot' => false,
])

@php
    // Filled glyphs — dock aesthetic (active = white, idle = muted grey).
    $icons = [
        'home' => '<path d="M11.2 3.3a1.2 1.2 0 0 1 1.6 0l7.4 6.5c.5.45.16 1.3-.5 1.3H18v8.1c0 .6-.5 1.1-1.1 1.1h-3.4v-5.2a.9.9 0 0 0-.9-.9H11.4a.9.9 0 0 0-.9.9v5.2H7.1c-.6 0-1.1-.5-1.1-1.1V11.1H4.3c-.66 0-1-.85-.5-1.3l7.4-6.5z"/>',
        'feed' => '<path fill-rule="evenodd" d="M7.5 4A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9zm4.5 3.6c-1.5-1.5-3.9-.35-3.9 1.55 0 2.15 2.4 3.75 3.9 4.8 1.5-1.05 3.9-2.65 3.9-4.8 0-1.9-2.4-3.05-3.9-1.55z"/>',
        'heart' => '<path d="M12 20.2S4.5 15.1 4.5 9.8C4.5 7.1 6.6 5 9.2 5c1.5 0 2.8.7 3.8 1.9C14 5.7 15.3 5 16.8 5c2.6 0 4.7 2.1 4.7 4.8 0 5.3-7.5 10.4-9.5 10.4z"/>',
        'clock' => '<path fill-rule="evenodd" d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zm.75 4.2a.75.75 0 0 0-1.5 0v5.05c0 .2.08.39.22.53l3.1 3.1a.75.75 0 1 0 1.06-1.06l-2.88-2.88V7.2z"/>',
        'chat' => '<path fill-rule="evenodd" d="M6.2 4.5h11.6A2.7 2.7 0 0 1 20.5 7.2v6.4a2.7 2.7 0 0 1-2.7 2.7h-5.2L8.2 20v-3.7H6.2A2.7 2.7 0 0 1 3.5 13.6V7.2a2.7 2.7 0 0 1 2.7-2.7zm1.9 5.2a.7.7 0 0 0 0 1.4h7.8a.7.7 0 0 0 0-1.4H8.1zm0 3a.7.7 0 0 0 0 1.4h5.2a.7.7 0 0 0 0-1.4H8.1z"/>',
        'compass' => '<path fill-rule="evenodd" d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zm4.1 4.7-5.5 2.1-2.1 5.5 5.5-2.1 2.1-5.5zM12 11.2a.8.8 0 1 0 0 1.6.8.8 0 0 0 0-1.6z"/>',
        'wallet' => '<path fill-rule="evenodd" d="M5 6.2A2.2 2.2 0 0 1 7.2 4h9.6A2.2 2.2 0 0 1 19 6.2V8h1.2c.7 0 1.3.6 1.3 1.3v7.5c0 .7-.6 1.3-1.3 1.3H19v.7A2.2 2.2 0 0 1 16.8 21H7.2A2.2 2.2 0 0 1 5 18.8V6.2zM19 9.5H7.5v8.8c0 .4.3.7.7.7h8.6c.4 0 .7-.3.7-.7v-.5H19V9.5zm-2.2 4.2a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2z"/>',
        'user' => '<path d="M12 12.2a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 1.6c-3.4 0-6.2 1.8-7.2 4.4-.2.5.2 1 .7 1h13c.5 0 .9-.5.7-1-1-2.6-3.8-4.4-7.2-4.4z"/>',
        'plus' => '<path fill-rule="evenodd" d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zm0 4a.9.9 0 0 1 .9.9V11h3.1a.9.9 0 1 1 0 1.8h-3.1v3.1a.9.9 0 1 1-1.8 0V12.8H8.9a.9.9 0 1 1 0-1.8h3.1V7.9A.9.9 0 0 1 12 7z"/>',
    ];
    $path = $icons[$icon] ?? $icons['home'];
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->class(['bottom-nav-item', 'is-active' => $active]) }}
    @if($active) aria-current="page" @endif
>
    <span class="bottom-nav-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="currentColor">{!! $path !!}</svg>
        @if($badgeDot)
            <x-atom.count-badge :count="1" variant="dot" />
        @elseif($badge !== null && (int) $badge > 0)
            <x-atom.count-badge :count="$badge" />
        @endif
    </span>
    <span class="bottom-nav-label">{{ $label }}</span>
</a>
