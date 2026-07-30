{{--
    Sub-sidebar — contextual second-level panel opened by a
    <x-sidebar.sub-trigger>. Must be rendered as a SIBLING of
    <x-sidebar.sidebar> (the sidebar clips fixed children).

    <x-slot:sidebar>
        <x-sidebar.sidebar>
            <x-sidebar.sub-trigger target="categories" icon="tag">All Categories</x-sidebar.sub-trigger>
        </x-sidebar.sidebar>

        <x-sidebar.sub-sidebar id="categories" title="Categories">
            <x-sidebar.group label="Appearance">
                <x-sidebar.link href="#">…</x-sidebar.link>
            </x-sidebar.group>
        </x-sidebar.sub-sidebar>
    </x-slot:sidebar>
--}}
@props([
    'id',
    'title' => null,
])

<section
    class="sub-sidebar"
    id="sub-sidebar-{{ $id }}"
    data-sub-sidebar="{{ $id }}"
    @if($title) aria-label="{{ $title }}" @endif
>
    <div class="sub-sidebar-head">
        <button type="button" class="sub-sidebar-back" data-sub-sidebar-close aria-label="{{ __('common.back') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </button>
        @if($title)
            <span class="sub-sidebar-title">{{ $title }}</span>
        @endif
    </div>

    <div class="sub-sidebar-scroll">
        <div class="sidebar-content">
            {{ $slot }}
        </div>
    </div>
</section>
