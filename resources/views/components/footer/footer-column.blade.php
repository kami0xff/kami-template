@props(['title'])

<ul class="footer--column-list">
    <span class="footer--column-list-header">{{ $title }}</span>
    {{ $slot }}
</ul>
