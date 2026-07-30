@props([
    'brand' => null,          // brand text, defaults to app name
    'description' => null,    // left-hand paragraph
    'backgroundTextArt' => false, // giant brand watermark — opt-in spec
])

@php
    $brand = $brand ?? config('app.name', 'App');
@endphp

<footer class="footer">
    <div class="footer-divider-top"></div>

    <div class="footer--column-layout">
        <div class="footer--left">
            @if($description)
                <p class="footer--left-text">{{ $description }}</p>
            @endif

            {{ $left ?? '' }}
        </div>

        @isset($columns)
            {{ $columns }}
        @else
            {{-- Default generic columns — override via the "columns" slot --}}
            <x-footer.footer-column title="Product">
                <x-link href="{{ localized_route('home') }}">Home</x-link>
                <x-link href="#">Features</x-link>
                <x-link href="#">Pricing</x-link>
            </x-footer.footer-column>

            <x-footer.footer-column title="Support">
                <x-link href="#">Help Center</x-link>
                <x-link href="#">Contact Us</x-link>
                <x-link href="#">Status</x-link>
            </x-footer.footer-column>

            <x-footer.footer-column title="Legal">
                <x-link href="#">Terms of Use</x-link>
                <x-link href="#">Privacy Policy</x-link>
                <x-link href="#">Cookies</x-link>
            </x-footer.footer-column>
        @endisset
    </div>

    <div class="footer--bottom">
        {{ $bottom ?? '' }}

        <div class="footer--divider-flat"></div>
        <div class="footer--legal-details">
            <div class="copyright">
                &copy; {{ date('Y') }} {{ $brand }}
            </div>

            @isset($legal)
                <div>{{ $legal }}</div>
            @endisset
        </div>
    </div>

    @if($backgroundTextArt)
        <div class="footer-background-text-art">{{ $brand }}</div>
    @endif
</footer>
