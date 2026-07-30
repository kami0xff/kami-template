@props(['id'])

<div {{ $attributes->merge(['class' => 'sidebar-dropdown']) }} data-dropdown="{{ $id }}">
    @if(isset($trigger))
        {{ $trigger }}
    @endif
    <div class="sidebar-dropdown-content" data-dropdown-content="{{ $id }}">
        {{ $slot }}
    </div>
</div>