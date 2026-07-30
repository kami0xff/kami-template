{{--
    Anchored panel (popover) — positions a floating panel relative to its
    trigger. Behavior comes from PopoverManager (outside click / Escape close).

    Usage:
        <x-panels.popover placement="bottom-end">
            <x-slot:trigger>
                <x-atom.button.button variant="muted" text="Details" />
            </x-slot:trigger>
            Any content…
        </x-panels.popover>
--}}
@props([
    'placement' => 'bottom-start', // bottom-start | bottom-end | top-start | top-end | right-start | left-start
    'hover' => false,              // open on hover/focus instead of click
    'bare' => false,               // no padding (for menus/custom content)
    'width' => null,
])

<div class="popover" data-popover @if($hover) data-popover-hover @endif>
    <span data-popover-trigger aria-haspopup="true" aria-expanded="false" style="display:inline-flex;">
        {{ $trigger }}
    </span>
    <div
        class="popover-panel popover-panel--{{ $placement }} {{ $bare ? 'popover-panel--bare' : '' }}"
        data-popover-panel
        @if($width) style="width: {{ $width }}" @endif
        {{ $attributes }}
    >
        {{ $slot }}
    </div>
</div>
