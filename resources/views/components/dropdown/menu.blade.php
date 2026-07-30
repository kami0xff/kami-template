{{--
    Dropdown menu — an anchored panel containing actions.

    Usage:
        <x-dropdown.menu placement="bottom-end">
            <x-slot:trigger>
                <x-atom.pill>More ▾</x-atom.pill>
            </x-slot:trigger>
            <x-dropdown.item href="/profile">Profile</x-dropdown.item>
            <x-dropdown.item hint="⌘K">Search</x-dropdown.item>
            <x-dropdown.separator />
            <x-dropdown.item :danger="true">Sign out</x-dropdown.item>
        </x-dropdown.menu>
--}}
@props([
    'placement' => 'bottom-start',
    'hover' => false,
])

<div class="popover" data-popover @if($hover) data-popover-hover @endif>
    <span data-popover-trigger aria-haspopup="menu" aria-expanded="false" style="display:inline-flex;">
        {{ $trigger }}
    </span>
    <div class="popover-panel popover-panel--{{ $placement }} popover-panel--bare" data-popover-panel>
        <div class="menu" role="menu" {{ $attributes }}>
            {{ $slot }}
        </div>
    </div>
</div>
