{{--
    <x-toolbar aria-label="Formatting">
        <x-atom.button.icon-button label="Bold">…</x-atom.button.icon-button>
        <span class="toolbar-separator"></span>
        <x-atom.button.icon-button label="Link">…</x-atom.button.icon-button>
    </x-toolbar>
--}}
<div {{ $attributes->class(['toolbar']) }} role="toolbar">
    {{ $slot }}
</div>
