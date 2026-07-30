@props(
    [
        'title' => null,
        'ariaLabel' => null
    ]
)

<div {{ $attributes->merge([
    'class' => 'dropdown-group',
    'aria-label' => $ariaLabel ?? $title,
]) }}>
    @if (isset($title))
        <div class="dropdown-group-title">{{$title}}</div>

    @endif
    <div class="dropdown-group-items">{{ $slot }}</div>
</div>
