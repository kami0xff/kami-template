{{--
    Pinned stories / Instagram Highlights row.

    Props:
        title — section heading (default: Highlights)
        meta  — optional right-side meta text

    Slot: <x-stories.ring state="pinned" label="Travel" cover="…" :items="[…]">
--}}
@props([
    'title' => 'Highlights',
    'meta' => null,
])

<section {{ $attributes->class(['stories-pinned']) }} data-stories-pinned>
    <div class="stories-pinned-head">
        <h3 class="stories-pinned-title">{{ $title }}</h3>
        @if($meta)
            <span class="stories-pinned-meta">{{ $meta }}</span>
        @endif
    </div>
    <div class="stories-tray stories-tray--pinned" data-stories-tray>
        {{ $slot }}
    </div>
</section>
