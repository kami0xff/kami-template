{{--
    Combobox — text field + suggestion list.

    <x-form.combobox name="model" placeholder="Search…" :open="true">
        <x-slot:suggestions>
            <div class="form-combo-section">Models</div>
            <button type="button" class="form-combo-item active" data-combo-item data-value="meghan">…</button>
        </x-slot:suggestions>
    </x-form.combobox>
--}}
@props([
    'name' => '',
    'value' => '',
    'placeholder' => 'Search…',
    'open' => false,
])

<div
    {{ $attributes->class(['form-combobox']) }}
    data-form-combobox
    data-open="{{ $open ? 'true' : 'false' }}"
>
    <x-form.input-wrap>
        <x-slot:icon>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
        </x-slot:icon>
        <x-form.input
            :name="$name"
            :value="$value"
            :placeholder="$placeholder"
            autocomplete="off"
            data-combo-input
            role="combobox"
            aria-expanded="{{ $open ? 'true' : 'false' }}"
            aria-autocomplete="list"
        />
    </x-form.input-wrap>
    <div class="form-combo-suggestions" data-combo-list @if(!$open) hidden @endif role="listbox">
        {{ $suggestions ?? $slot }}
    </div>
</div>
