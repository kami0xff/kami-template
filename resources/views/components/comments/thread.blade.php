{{--
    Comments thread shell.

    Props:
        title, count
    Slots:
        composer (optional) — defaults to a top-level composer
        default — comment items
--}}
@props([
    'title' => 'Comments',
    'count' => null,
])

<section {{ $attributes->class(['comments']) }} data-comments>
    <div class="comments-heading">
        <h3>{{ $title }}</h3>
        @if(!is_null($count))
            <span class="comments-count" data-comments-count>{{ $count }}</span>
        @endif
    </div>

    @isset($composer)
        {{ $composer }}
    @else
        <x-comments.composer avatar="https://i.pravatar.cc/64?img=5" name="You" />
    @endisset

    <div class="comments-list" data-comments-list>
        {{ $slot }}
    </div>
</section>
