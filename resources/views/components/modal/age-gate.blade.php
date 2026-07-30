{{--
    Age / consent gate modal.

    Mount once (e.g. app-shell overlays). Auto-opens when localStorage key is missing
    unless :auto="false".

    <x-modal.age-gate id="age-gate" :min-age="18" />
--}}
@props([
    'id' => 'age-gate',
    'minAge' => 18,
    'storageKey' => 'kami.age-gate.ok',
    'auto' => true,
    'title' => null,
    'body' => null,
])

@php
    $title = $title ?? "Are you {$minAge} or older?";
    $body = $body ?? 'This site contains adult content. You must be of legal age in your jurisdiction to enter.';
@endphp

<div
    data-age-gate
    data-age-gate-id="{{ $id }}"
    data-storage-key="{{ $storageKey }}"
    data-auto="{{ $auto ? '1' : '0' }}"
    hidden
>
    <x-modal.modal :id="$id" :title="$title" maxWidth="420px" {{ $attributes }}>
        <p style="margin:0; color:var(--text-muted); line-height:1.45;">{{ $body }}</p>
        <x-slot:footer>
            <x-atom.button.button
                variant="muted"
                text="Leave"
                data-age-leave
                onclick="window.location.href='https://www.google.com'"
            />
            <x-atom.button.button
                variant="primary"
                text="I am {{ $minAge }}+"
                data-age-enter
            />
        </x-slot:footer>
    </x-modal.modal>
</div>
