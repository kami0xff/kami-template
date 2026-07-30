{{--
    <x-description-list :items="[
        'Username' => 'kami_x',
        'Country' => 'Ukraine',
        'Member since' => 'March 2024',
    ]" />
--}}
@props([
    'items' => [],
])

<dl {{ $attributes->class(['description-list']) }}>
    @foreach($items as $term => $description)
        <div>
            <dt>{{ $term }}</dt>
            <dd>{{ $description }}</dd>
        </div>
    @endforeach
</dl>
