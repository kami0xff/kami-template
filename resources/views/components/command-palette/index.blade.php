{{--
    ⌘K command palette. Render once per page (e.g. in the app shell) and
    pass items; extra search terms go in `keywords`.

    <x-command-palette :items="[
        ['label' => 'Home', 'href' => '/', 'hint' => 'G H'],
        ['label' => 'Buy tokens', 'href' => '/tokens', 'keywords' => 'payment credits'],
    ]" />
--}}
@props([
    'items' => [],
    'placeholder' => 'Search actions and pages…',
])

<div class="command-overlay" data-command-palette>
    <div class="command-panel" role="dialog" aria-modal="true" aria-label="Command palette">
        <input type="text" class="command-input" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}">
        <div class="command-list" role="listbox">
            @foreach($items as $item)
                <a
                    class="command-item"
                    href="{{ $item['href'] ?? '#' }}"
                    role="option"
                    @if(!empty($item['keywords'])) data-keywords="{{ $item['keywords'] }}" @endif
                >
                    <span>{{ $item['label'] }}</span>
                    @if(!empty($item['hint']))
                        <span class="command-item-hint">{{ $item['hint'] }}</span>
                    @endif
                </a>
            @endforeach
            {{ $slot }}
        </div>
        <div class="command-empty" hidden>No results</div>
    </div>
</div>
