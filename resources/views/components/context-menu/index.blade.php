{{--
    Right-click menu. Attach with data-context-menu="{id}" on any region;
    fill with <x-dropdown.item> / <x-dropdown.separator>.

    <div data-context-menu="photo-menu">…</div>
    <x-context-menu id="photo-menu">
        <x-dropdown.item>Save photo</x-dropdown.item>
        <x-dropdown.separator />
        <x-dropdown.item :danger="true">Report</x-dropdown.item>
    </x-context-menu>
--}}
@props([
    'id',
])

<div class="context-menu" id="{{ $id }}">
    <div class="menu" role="menu" {{ $attributes }}>
        {{ $slot }}
    </div>
</div>
