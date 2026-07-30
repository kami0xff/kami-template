{{--
    Avatar upload control.

    <x-form.avatar-upload name="avatar" src="…" />
--}}
@props([
    'name' => 'avatar',
    'src' => null,
    'alt' => '',
])

<label
    {{ $attributes->class(['form-avatar-upload', 'has-image' => (bool) $src]) }}
    data-form-avatar-upload
>
    <input type="file" name="{{ $name }}" accept="image/*" hidden data-avatar-input>
    <span class="form-avatar-upload-media" aria-hidden="{{ $src ? 'false' : 'true' }}">
        @if($src)
            <img data-avatar-preview src="{{ $src }}" alt="{{ $alt }}">
            <span data-avatar-placeholder hidden>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"/><path d="M4 20a8 8 0 0 1 16 0"/>
                </svg>
            </span>
        @else
            <img data-avatar-preview alt="{{ $alt }}" hidden>
            <span data-avatar-placeholder>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"/><path d="M4 20a8 8 0 0 1 16 0"/>
                </svg>
            </span>
        @endif
    </span>
    <span class="form-avatar-edit" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
        </svg>
    </span>
</label>
