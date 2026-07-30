{{--
    Attached button group. Mark the active segment with .btn--group-active:

    <x-atom.button.group>
        <button class="btn btn-muted btn--group-active">Grid</button>
        <button class="btn btn-muted">List</button>
    </x-atom.button.group>
--}}
<div {{ $attributes->class(['btn-group']) }} role="group">
    {{ $slot }}
</div>
