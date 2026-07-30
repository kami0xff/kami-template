@props([
    'id' => '',
    'active' => false,
])

<div
    role="tabpanel"
    data-tab-panel-id="{{ $id }}"
    data-active="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->class(['tab-panel', 'tab-active' => $active]) }}
>
    {{ $slot }}
</div>
