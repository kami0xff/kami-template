@props([
    'items' => [],
    'active' => null,
])

<div {{ $attributes->class(['form-segmented']) }}>
    @foreach($items as $key => $label)
        <button
            type="button"
            class="form-seg {{ ($active ?? $loop->first) && (is_null($active) ? $loop->first : $key === $active) ? 'active' : '' }}"
            data-value="{{ $key }}"
        >{{ $label }}</button>
    @endforeach
</div>
