@props([
    'title' => '',
    'description' => '',
])

<fieldset {{ $attributes->class(['form-group']) }}>
    @if($title || $description)
        <div class="form-group-header">
            @if($title)
                <legend class="form-group-title">{{ $title }}</legend>
            @endif
            @if($description)
                <p class="form-group-description">{{ $description }}</p>
            @endif
        </div>
    @endif

    {{ $slot }}
</fieldset>
