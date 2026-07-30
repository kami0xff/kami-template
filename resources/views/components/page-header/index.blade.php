{{--
    Page / section header.

    <x-page-header title="Wallet" description="Buy tokens and review history">
        <x-slot:actions>
            <x-atom.button.button variant="primary" text="Buy tokens" />
        </x-slot:actions>
    </x-page-header>
--}}
@props([
    'title' => '',
    'description' => null,
    'eyebrow' => null,
])

<header {{ $attributes->class(['page-header']) }} data-page-header>
    <div class="page-header-copy">
        @if($eyebrow)<p class="page-header-eyebrow">{{ $eyebrow }}</p>@endif
        <h1 class="page-header-title">{{ $title }}</h1>
        @if($description)<p class="page-header-desc">{{ $description }}</p>@endif
        {{ $slot }}
    </div>
    @isset($actions)
        <div class="page-header-actions">{{ $actions }}</div>
    @endisset
</header>
