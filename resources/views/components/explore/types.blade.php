{{--
    Media-type toggle row.

    Props:
        types — array of ['id','label','count'?, 'on'? => bool, 'icon'? => live|image|gallery|gif|video|manga|ai]
--}}
@props([
    'types' => [
        ['id' => 'live', 'label' => 'Live', 'on' => true],
        ['id' => 'image', 'label' => 'Images', 'on' => true],
        ['id' => 'gallery', 'label' => 'Galleries', 'on' => true],
        ['id' => 'gif', 'label' => 'GIFs', 'on' => true],
        ['id' => 'video', 'label' => 'Videos', 'on' => true],
        ['id' => 'manga', 'label' => 'Manga', 'on' => true],
        ['id' => 'ai', 'label' => 'AI', 'on' => true],
    ],
])

@php
    $icons = [
        'live' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M5 12a7 7 0 0 1 14 0M2 12a10 10 0 0 1 20 0"/></svg>',
        'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path d="m21 15-4.5-4.5L9 18"/></svg>',
        'gallery' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="7" width="14" height="12" rx="1.5"/><path d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h9A1.5 1.5 0 0 1 20 5.5V15"/></svg>',
        'gif' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M8 15V9h3.2M8 12h2.4M14 9v6M17 9v6"/></svg>',
        'video' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3z"/></svg>',
        'manga' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h7v14H4zM13 5h7v14h-7z"/><path d="M8.5 8h0M15.5 8h0"/></svg>',
        'ai' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4 7 17M17 7l1.4-1.4"/></svg>',
    ];
@endphp

<div class="explore-bar-row" data-explore-types-row>
    <span class="explore-bar-label">Media</span>
    <div class="explore-scroller" role="group" aria-label="Media types">
        <div class="explore-types" data-explore-types>
            @foreach($types as $type)
                @php
                    $id = $type['id'] ?? '';
                    $label = $type['label'] ?? $id;
                    $on = array_key_exists('on', $type) ? (bool) $type['on'] : true;
                    $count = $type['count'] ?? null;
                    $iconKey = $type['icon'] ?? $id;
                    $icon = $icons[$iconKey] ?? $icons['image'];
                @endphp
                <button
                    type="button"
                    class="explore-chip explore-chip--{{ $id }}{{ $on ? ' is-on' : ' is-off' }}"
                    data-explore-type="{{ $id }}"
                    aria-pressed="{{ $on ? 'true' : 'false' }}"
                >
                    <span class="explore-chip-icon">{!! $icon !!}</span>
                    <span>{{ $label }}</span>
                    @if(!is_null($count))
                        <span class="explore-chip-count">{{ number_format((int) $count) }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <div class="explore-bar-actions">
        <button type="button" class="explore-bar-action" data-explore-types-all>All</button>
        <button type="button" class="explore-bar-action" data-explore-types-none>None</button>
    </div>
</div>
