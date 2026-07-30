@props([
    'label',
    'href' => null,
    'open' => false,
])

@if(trim($slot))
    <details {{ $attributes->class(['tree-branch']) }} @if($open) open @endif>
        <summary class="tree-row">
            <svg class="tree-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
            <span>{{ $label }}</span>
        </summary>
        <div class="tree">
            {{ $slot }}
        </div>
    </details>
@elseif($href)
    <a href="{{ $href }}" {{ $attributes->class(['tree-row', 'tree-leaf']) }}>{{ $label }}</a>
@else
    <span {{ $attributes->class(['tree-row', 'tree-leaf']) }}>{{ $label }}</span>
@endif
