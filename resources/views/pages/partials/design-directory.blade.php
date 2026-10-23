<x-directory :title="$compact ? null : 'Categories'" search="Find categories" :compact="$compact">
    <x-directory.section title="Appearance">
        @foreach ($demoDirectory as $label => $items)
            <x-directory.group :label="$label">
                @foreach ($items as $name => $count)
                    <x-directory.item href="#" :count="$count" count-label="Live now">{{ $name }}</x-directory.item>
                @endforeach
            </x-directory.group>
        @endforeach
    </x-directory.section>
    <x-directory.section title="Activities on request">
        <x-directory.group label="Activities" :wide="true">
            @foreach ($demoActivities as $name => $count)
                <x-directory.item href="#" :count="$count">{{ $name }}</x-directory.item>
            @endforeach
        </x-directory.group>
    </x-directory.section>
</x-directory>
