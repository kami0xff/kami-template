{{--
    Radio list (Astryx Radio List) — vertical or horizontal plain radios.

    <x-form.radio-list
        name="notify"
        label="Notifications"
        :items="[
            ['value' => 'all', 'label' => 'All activity'],
            ['value' => 'mentions', 'label' => 'Mentions only'],
            ['value' => 'off', 'label' => 'Off'],
        ]"
        value="mentions"
        orientation="vertical"
    />

    Or slot custom items (e.g. <x-form.radio.item> select-rows).
--}}
@props([
    'name' => '',
    'label' => null,
    'description' => null,
    'items' => [],
    'value' => '',
    'orientation' => 'vertical', // vertical | horizontal
    'disabled' => false,
])

@php
    $current = old($name, $value);
@endphp

<div {{ $attributes->class([
    'form-radio-list',
    'form-radio-list--' . ($orientation === 'horizontal' ? 'horizontal' : 'vertical'),
]) }}>
    @if($label)
        <div class="form-radio-list-label">{{ $label }}</div>
    @endif
    @if($description)
        <p class="form-radio-list-desc">{{ $description }}</p>
    @endif

    <div
        class="form-radio-list-items"
        role="radiogroup"
        @if($label) aria-label="{{ $label }}" @endif
    >
        @if(count($items))
            @foreach($items as $item)
                <x-form.radio
                    :name="$name"
                    :value="$item['value']"
                    :label="$item['label'] ?? $item['value']"
                    :checked="(string) $current === (string) $item['value']"
                    :disabled="$disabled || !empty($item['disabled'])"
                />
            @endforeach
        @else
            {{ $slot }}
        @endif
    </div>
</div>
