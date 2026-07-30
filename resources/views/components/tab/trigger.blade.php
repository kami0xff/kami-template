@props([
    'id' => '',
    'active' => false,
    'tag' => 'button',
])

@if($tag === 'button')
<button
    type="button"
    role="tab"
    aria-selected="{{ $active ? 'true' : 'false' }}"
    data-tab-trigger-id="{{ $id }}"
    data-active="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->class(['tab-trigger', 'tab-active' => $active]) }}
>
    {{ $slot }}
</button>
@else
<{{ $tag }}
    role="tab"
    aria-selected="{{ $active ? 'true' : 'false' }}"
    data-tab-trigger-id="{{ $id }}"
    data-active="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->class(['tab-trigger', 'tab-active' => $active]) }}
>
    {{ $slot }}
</{{ $tag }}>
@endif
