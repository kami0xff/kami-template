@props([
    'href' => null,
    'params' => null,
    'action' => null,
    'target' => null,
])
@if (isset($href))
     <a
        {{ $attributes->merge([
            'class' => 'dropdown-item',
        ])}}
        href="{{ localized_route($href, $params ? (is_array($params) ? $params : [$params]) : []) }}"
    >
        {{ $slot }}
    </a>
@else
    <button type="button"
        {{ $attributes->merge([
            'class' => 'dropdown-item',
        ])}}
        data-action="{{ $action }}"
        data-target="{{ $target }}">
            {{ $slot }}
    </button>
@endif