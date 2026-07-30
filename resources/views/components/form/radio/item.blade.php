@aware(['name' => ''])

@props([
    'name' => '',
    'value' => '',
    'checked' => false,
    'title' => '',
    'subtitle' => null,
    'price' => null,
    'save' => null,
])

<label {{ $attributes->class(['select-row']) }}>
    <input type="radio" name="{{ $name }}" value="{{ $value }}" class="sr-only" @checked($checked)>
    <div class="select-radio"></div>
    <div class="select-row-main">
        <div class="select-row-title">{{ $title }}</div>
        @if($subtitle)
            <div class="select-row-sub">{{ $subtitle }}</div>
        @endif
    </div>
    @if($price || isset($right))
        <div class="select-row-right">
            @if(isset($right))
                {{ $right }}
            @else
                <div class="select-price">{{ $price }}</div>
                @if($save)
                    <div class="select-price-save">{{ $save }}</div>
                @endif
            @endif
        </div>
    @endif
</label>
