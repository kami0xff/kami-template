@props([
    'variant' => 'neutral', // neutral | primary | success | danger | warning | info | money
    'dot' => null,          // live | busy | away | offline — prepends a status dot
])

<span {{ $attributes->class(['badge', "badge--$variant" => $variant !== 'neutral']) }}>
    @if($dot)
        <span class="status-dot status-dot--{{ $dot }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
