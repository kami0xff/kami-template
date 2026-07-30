{{--
    Tag input — chips + inline entry.

    <x-form.tags name="niches" :tags="['blonde','cosplay']" placeholder="Add a tag…" />
--}}
@props([
    'name' => 'tags',
    'tags' => [],
    'placeholder' => 'Add a tag…',
    'accent' => true,
])

<div
    {{ $attributes->class(['form-tag-input']) }}
    data-form-tags
    data-name="{{ $name }}"
>
    @foreach($tags as $tag)
        <span class="form-tag {{ $accent ? 'accent' : '' }}" data-tag="{{ $tag }}">
            {{ $tag }}
            <button type="button" class="form-tag-remove" data-tag-remove aria-label="Remove {{ $tag }}">&times;</button>
            <input type="hidden" name="{{ $name }}[]" value="{{ $tag }}">
        </span>
    @endforeach
    <input type="text" class="form-tag-input-field" data-tag-field placeholder="{{ $placeholder }}" autocomplete="off">
</div>
