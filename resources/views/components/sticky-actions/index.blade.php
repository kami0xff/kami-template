{{--
    Sticky bottom action bar (mobile tip / subscribe / CTA).

    <x-sticky-actions>
        <x-atom.button.button variant="primary" text="Send tip" />
        <x-follow-button />
    </x-sticky-actions>
--}}
@props([
    'elevated' => true,
])

<div
    {{ $attributes->class(['sticky-actions', 'sticky-actions--elevated' => $elevated]) }}
    data-sticky-actions
>
    <div class="sticky-actions-inner">
        {{ $slot }}
    </div>
</div>
