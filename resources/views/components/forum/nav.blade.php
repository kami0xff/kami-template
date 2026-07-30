{{--
    Horizontal board list — classic imageboard top strip.

    Props: boards (array of ['code' => 'g', 'href' => '#', 'label' => optional, 'active' => bool])
    Slots: aside — right-side links (Home, Catalog, …)
--}}
@props([
    'boards' => [],
])

<nav {{ $attributes->class(['forum-nav']) }} data-forum-nav aria-label="Boards">
    <ul class="forum-nav-list">
        @foreach($boards as $board)
            @php
                $code = is_array($board) ? ($board['code'] ?? '') : (string) $board;
                $href = is_array($board) ? ($board['href'] ?? '#') : '#';
                $label = is_array($board) ? ($board['label'] ?? $code) : $code;
                $active = is_array($board) && !empty($board['active']);
            @endphp
            <li class="forum-nav-item">
                <a
                    href="{{ $href }}"
                    class="forum-nav-link{{ $active ? ' is-active' : '' }}"
                    @if($active) aria-current="page" @endif
                >{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    @isset($aside)
        <div class="forum-nav-aside">
            {{ $aside }}
        </div>
    @endisset
</nav>
