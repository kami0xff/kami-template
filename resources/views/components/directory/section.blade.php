{{--
    Titled block of directory groups (e.g. "Appearance", "Tags").

    Props: title, id (anchor, e.g. #tags)
    Slots: icon (before the title), default (x-directory.group items)
--}}
@props([
    'title',
    'id' => null,
])

<section {{ $attributes->class(['directory-section']) }} @if ($id) id="{{ $id }}" @endif data-directory-section>
    <h2 class="directory-section-title">
        @isset($icon)<span class="directory-section-icon" aria-hidden="true">{{ $icon }}</span>@endisset
        {{ $title }}
    </h2>
    <div class="directory-groups">
        {{ $slot }}
    </div>
</section>
