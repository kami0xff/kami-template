@props([
    'align' => 'end',
])

<div {{ $attributes->class([
    'form-actions',
    'form-actions--end' => $align === 'end',
    'form-actions--between' => $align === 'between',
]) }}>
    {{ $slot }}
</div>
