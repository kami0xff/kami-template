{{--
    User avatar (Astryx: Avatar).

    Fallback chain: image → initials (from name) → person glyph.
    Renders a real <button> when interactive (focus ring, aria-label);
    add aria-expanded / data-open from the opener for the active ring.

    Props:
        url          — avatar image URL
        name         — used for initials fallback + alt/aria-label
        size         — xs | sm | md | lg
        interactive  — render as <button>
        notification — top-right alert dot (accent)
        status       — online | live | busy | away — bottom-right dot

    Usage:
        <x-user-avatar url="…" size="sm" />
        <x-user-avatar name="Kami Dev" :interactive="true" :notification="true"
            onclick="panels.toggle('user-panel')" />
--}}
@props([
    'url' => null,
    'name' => null,
    'size' => 'md',
    'interactive' => false,
    'notification' => false,
    'status' => null,
])

@php
    $initials = $name
        ? collect(explode(' ', trim($name)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('')
        : null;
    $tag = $interactive ? 'button' : 'span';
@endphp

<{{ $tag }}
    @if($interactive) type="button" aria-label="{{ $name ?? 'Account' }}" @endif
    @class([
        'user-avatar-wrap',
        'user-avatar-wrap--' . $size,
        'user-avatar-wrap--interactive' => $interactive,
    ])
    {{ $attributes }}
>
    <span class="user-avatar">
        @if($url)
            <img src="{{ $url }}" alt="{{ $name ?? '' }}" class="avatar-img" loading="lazy">
        @elseif($initials)
            <span class="avatar-initials">{{ mb_strtoupper($initials) }}</span>
        @else
            <svg class="avatar-fallback" viewBox="0 0 80 110" fill="none" aria-hidden="true">
                <ellipse cx="40" cy="22" rx="16" ry="18" fill="currentColor" />
                <path d="M12 110 Q12 62 40 58 Q68 62 68 110Z" fill="currentColor" />
            </svg>
        @endif
    </span>

    @if($notification)
        <span class="avatar-notification"></span>
    @endif

    @if($status)
        <span class="avatar-status avatar-status--{{ $status }}"></span>
    @endif
</{{ $tag }}>
