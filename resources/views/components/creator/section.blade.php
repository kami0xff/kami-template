{{--
    One creator custom section.

    Props:
        id, title, type (socials|ad|links|text|custom)
--}}
@props([
    'id' => null,
    'title' => 'Section',
    'type' => 'custom',
])

@php
    $id = $id ?? 'section-' . uniqid();
@endphp

<section
    {{ $attributes->class(['creator-section']) }}
    data-creator-section
    data-creator-section-id="{{ $id }}"
    data-creator-section-type="{{ $type }}"
>
    <header class="creator-section-head">
        <h3 class="creator-section-title" data-creator-view-only data-creator-section-title>{{ $title }}</h3>
        <input
            class="creator-section-title-input"
            data-creator-edit-only
            data-creator-section-title-input
            type="text"
            value="{{ $title }}"
            aria-label="Section title"
        >

        <div class="creator-section-actions" data-creator-edit-only>
            <button type="button" class="creator-section-action" data-creator-move="up" title="Move up" aria-label="Move up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            </button>
            <button type="button" class="creator-section-action" data-creator-move="down" title="Move down" aria-label="Move down">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
            </button>
            <button type="button" class="creator-section-action creator-section-action--danger" data-creator-remove title="Remove" aria-label="Remove section">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>
            </button>
        </div>
    </header>

    <div class="creator-section-body">
        {{ $slot }}
    </div>
</section>
