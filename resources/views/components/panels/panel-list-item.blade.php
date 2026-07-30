<!-- resources/views/components/panels/item.blade.php -->
@props(['href' => '#', 'icon' => ''])

<a href="{{ $href }}" class="panel-list-item">
    {{--icons display logic --}}
    @if($icon)
        <x-dynamic-component :component="'icons.' . $icon" />
    @endif
    {{ $slot }}
</a>
