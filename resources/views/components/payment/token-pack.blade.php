{{-- Alias — prefer <x-wallet.pack>. Kept so older references keep working. --}}
@props([
    'amount' => 100,
    'price' => '',
    'value' => null,
    'name' => 'pack',
    'checked' => false,
    'save' => null,
    'badge' => null,
    'hint' => null,
])

<x-wallet.pack
    :amount="$amount"
    :price="$price"
    :value="$value"
    :name="$name"
    :checked="$checked"
    :save="$save"
    :badge="$badge"
    :hint="$hint"
    {{ $attributes }}
/>
