{{--
    Collapsible board rules block.
--}}
@props([
    'title' => 'Board rules',
    'open' => false,
])

<details {{ $attributes->class(['forum-rules']) }} @if($open) open @endif data-forum-rules>
    <summary>{{ $title }}</summary>
    <div class="forum-rules-body">
        {{ $slot }}
    </div>
</details>
