{{--
    Stories tray — horizontal Instagram-style ring strip.

    Props:
        label — optional section title above the tray

    Slot: <x-stories.ring>…</x-stories.ring>

    Usage:
        <x-stories>
            <x-stories.ring name="You" state="add" :self="true" avatar="…" />
            <x-stories.ring name="Maya" state="unseen" avatar="…" :items="[…]" />
        </x-stories>
--}}
@props([
    'label' => null,
])

<section {{ $attributes->class(['stories']) }} data-stories>
    @if($label)
        <div class="stories-pinned-head">
            <h3 class="stories-pinned-title">{{ $label }}</h3>
        </div>
    @endif
    <div class="stories-tray" data-stories-tray>
        {{ $slot }}
    </div>
</section>
