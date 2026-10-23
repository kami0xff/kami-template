{{--
    Directory — every category / tag of a site: titled sections of column
    groups, filtered in place by a search box and a Main/#/A–Z bar
    (DirectoryManager). Works as a full page or inside a modal / drawer.

    <x-directory title="Categories" search="Find categories and tags">
        <x-directory.section title="Appearance">
            <x-slot:icon><svg …/></x-slot:icon>
            <x-directory.group label="Age">
                <x-directory.item href="/c/milf" count="1,648">MILF</x-directory.item>
            </x-directory.group>
            <x-directory.group label="Activities" :wide="true">…</x-directory.group>
        </x-directory.section>
    </x-directory>

    Props:
        title    — heading beside the search box; null when a modal already titles it
        heading  — tag for the title (default h1; use h2 inside overlays)
        search   — filter box placeholder; null hides the box
        letters  — show the Main/#/A–Z bar (default true; revealed by JS)
        compact  — tighter spacing for overlays
        empty    — shown when the filter matches nothing
    Slots: actions (right of the search, e.g. a select), default (sections)
--}}
@props([
    'title' => null,
    'heading' => 'h1',
    'search' => null,
    'letters' => true,
    'compact' => false,
    'empty' => null,
])

<div {{ $attributes->class(['directory', 'directory--compact' => $compact]) }} data-directory>
    @if ($title || $search || isset($actions))
        <div class="directory-top">
            @if ($title)
                <{{ $heading }} class="directory-title">{{ $title }}</{{ $heading }}>
            @endif
            @if ($search)
                <div class="directory-search">
                    <x-form.input type="search" :placeholder="$search" :aria-label="$search" autocomplete="off" data-directory-filter />
                </div>
            @endif
            @isset($actions)
                <div class="directory-actions">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    @if ($letters)
        <nav class="directory-letters" aria-label="{{ __('By letter') }}" data-directory-letters hidden>
            <button type="button" class="directory-letter" data-directory-letter="" aria-pressed="true">{{ __('Main') }}</button>
            @foreach (array_merge(['#'], range('A', 'Z')) as $letter)
                <button type="button" class="directory-letter" data-directory-letter="{{ $letter }}" aria-pressed="false">{{ $letter }}</button>
            @endforeach
        </nav>
    @endif

    {{ $slot }}

    <p class="directory-empty" data-directory-empty hidden>{{ $empty ?? __('Nothing matches.') }}</p>
</div>
