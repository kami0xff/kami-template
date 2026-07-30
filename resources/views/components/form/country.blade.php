{{--
    Searchable country dropdown with flag icons.

    <x-form.country name="country" selected="FR" />
--}}
@props([
    'name' => 'country',
    'selected' => '',
    'placeholder' => 'Select country…',
    'disabled' => false,
    'only' => null, // optional list of ISO codes to limit options
])

@php
    $all = require resource_path('data/countries.php');
    if (is_array($only) && count($only)) {
        $codes = array_map('strtoupper', $only);
        $all = array_intersect_key($all, array_flip($codes));
    }

    $options = [];
    foreach ($all as $code => $label) {
        $options[] = [
            'value' => $code,
            'label' => $label,
            'meta' => $code,
            'icon' => '<span class="fi fi-' . strtolower($code) . '" aria-hidden="true"></span>',
        ];
    }
@endphp

<x-form.selector
    :name="$name"
    :options="$options"
    :selected="$selected"
    :placeholder="$placeholder"
    :disabled="$disabled"
    :searchable="true"
    search-placeholder="Search countries…"
    empty-text="No countries match"
    {{ $attributes->class(['form-country']) }}
/>
