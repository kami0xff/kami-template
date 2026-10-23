{{--
    One column of a directory section under a grey label bar.

    Props:
        label — the bar text (rendered uppercase)
        wide  — take the whole row and flow the items into columns,
                for long lists ("More", "Activities", every tag)
--}}
@props([
    'label',
    'wide' => false,
])

<div {{ $attributes->class(['directory-group', 'directory-group--wide' => $wide]) }} data-directory-group>
    <h3 class="directory-head">{{ $label }}</h3>
    <ul class="directory-list">
        {{ $slot }}
    </ul>
</div>
