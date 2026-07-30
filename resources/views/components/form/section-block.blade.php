@props([
    'open' => false,
    'title' => '',
    'subtitle' => '',
    'icon' => null,
])

<div {{ $attributes->class([
    'form-section-block',
    'open' => $open,
]) }}>
    <button class="form-section-head" type="button" onclick="this.closest('.form-section-block').classList.toggle('open')">
        @if($icon)
            <span class="form-section-icon">{!! $icon !!}</span>
        @endif
        <span class="form-section-meta">
            <strong>{{ $title }}</strong>
            @if($subtitle)
                <span>{{ $subtitle }}</span>
            @endif
        </span>
        <svg class="form-section-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div class="form-section-body">
        {{ $slot }}
    </div>
</div>
