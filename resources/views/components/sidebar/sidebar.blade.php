<!-- sidebar rail on small displays should also be possible here -->
<aside {{ $attributes->merge(['class' => 'sidebar', 'id' => 'sidebar']) }}>
    <div class="sidebar-scroll" data-sidebar-scroll>
        <div class="sidebar-content">
            {{ $slot }}
        </div>
    </div>
</aside>