{{--
    <x-tree>
        <x-tree.item label="Categories" :open="true">
            <x-tree.item label="Fetish" href="/c/fetish" />
            <x-tree.item label="Couples" href="/c/couples" />
        </x-tree.item>
        <x-tree.item label="Settings" href="/settings" />
    </x-tree>
--}}
<div {{ $attributes->class(['tree']) }} role="tree">
    {{ $slot }}
</div>
