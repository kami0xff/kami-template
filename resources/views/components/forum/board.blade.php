{{--
    Board page shell.

    Props: code, title, description, banner, threads, posts
    Slots: nav, rules, actions, default (catalog / index / thread)
--}}
@props([
    'code' => null,
    'title' => 'Board',
    'description' => null,
    'banner' => null,
    'threads' => null,
    'posts' => null,
])

<section {{ $attributes->class(['forum', 'forum-board']) }} data-forum data-forum-board="{{ $code }}">
    @isset($nav)
        {{ $nav }}
    @endisset

    <header class="forum-board-header">
        @if($banner)
            <img class="forum-board-banner" src="{{ $banner }}" alt="" loading="lazy" decoding="async">
        @endif

        <h1 class="forum-board-title">
            @if($code)
                <code>/{{ $code }}/</code>
            @endif
            {{ $title }}
        </h1>

        @if($description)
            <p class="forum-board-desc">{{ $description }}</p>
        @endif

        @if(!is_null($threads) || !is_null($posts))
            <div class="forum-board-meta">
                @if(!is_null($threads))
                    <span>{{ number_format((int) $threads) }} threads</span>
                @endif
                @if(!is_null($posts))
                    <span>{{ number_format((int) $posts) }} posts</span>
                @endif
            </div>
        @endif

        @isset($actions)
            <div class="forum-board-actions">
                {{ $actions }}
            </div>
        @endisset
    </header>

    @isset($rules)
        {{ $rules }}
    @endisset

    {{ $slot }}
</section>
