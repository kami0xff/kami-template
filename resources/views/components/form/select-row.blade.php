{{--
    Selectable plan / payment row (select-row chrome).

    <x-form.select-row name="plan" value="yearly" title="Yearly" subtitle="Billed annually" price="$79" save="save 33%" :selected="true" />
--}}
@props([
    'name' => '',
    'value' => '',
    'title' => '',
    'subtitle' => null,
    'price' => null,
    'save' => null,
    'selected' => false,
    'type' => 'radio', // radio | button
])

<label
    {{ $attributes->class(['select-row', 'selected' => $selected]) }}
    data-form-select-row
>
    @if($type === 'radio' && $name)
        <input type="radio" name="{{ $name }}" value="{{ $value }}" @checked($selected) class="select-radio" data-select-row-input>
    @else
        <span class="select-radio" aria-hidden="true"></span>
    @endif
    <span class="select-row-main">
        <span class="select-row-title">{{ $title }}</span>
        @if($subtitle)<span class="select-row-sub">{{ $subtitle }}</span>@endif
    </span>
    @if($price || $save)
        <span class="select-row-right">
            @if($price)<span class="select-price">{{ $price }}</span>@endif
            @if($save)<span class="select-price-save">{{ $save }}</span>@endif
        </span>
    @endif
</label>
