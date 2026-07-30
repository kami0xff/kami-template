{{--
    Generic list row — avatar + title/meta + trailing.

    <x-list-row title="Maya" meta="Started a private show" time="2m" href="#">
        <x-slot:leading><x-user-avatar name="Maya" size="sm" /></x-slot:leading>
        <x-slot:trailing><x-atom.count-badge :count="1" /></x-slot:trailing>
    </x-list-row>
--}}
@props([
    'title' => '',
    'meta' => null,
    'time' => null,
    'href' => null,
    'active' => false,
])

@php $tag = $href ? 'a' : 'div'; @endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->class(['list-row', 'list-row--active' => $active]) }}
    data-list-row
>
    @isset($leading)
        <div class="list-row-leading">{{ $leading }}</div>
    @endisset
    <div class="list-row-body">
        <div class="list-row-title-row">
            <span class="list-row-title">{{ $title }}</span>
            @if($time)<span class="list-row-time">{{ $time }}</span>@endif
        </div>
        @if($meta)<p class="list-row-meta">{{ $meta }}</p>@endif
        {{ $slot }}
    </div>
    @isset($trailing)
        <div class="list-row-trailing">{{ $trailing }}</div>
    @endisset
</{{ $tag }}>
