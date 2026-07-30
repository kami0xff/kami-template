{{--
    Wrapper for Magic Search example chips.

    Props:
        label — section label above the chips
--}}
@props(['label' => 'Exemples de Magic Search :'])

<div {{ $attributes->merge(['class' => 'search-examples']) }}>
    <div class="search-examples-label">{{ $label }}</div>
    <div class="search-examples-list">
        {{ $slot }}
    </div>
</div>
