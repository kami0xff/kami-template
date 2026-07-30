{{--
    Country flag — thin wrapper around flag-icons (https://flagicons.lipis.dev).
    Stylesheet: public/vendor/flag-icons (linked in layouts/app.blade.php).

    Props:
        code    — ISO 3166-1-alpha-2 country code (fr, ua, us, …)
        squared — 1:1 format instead of 4:3

    Usage:
        <x-atom.flag code="fr" />
        <x-atom.flag code="ua" :squared="true" />
--}}
@props([
    'code',
    'squared' => false,
])

<span {{ $attributes->merge(['class' => 'fi fi-' . strtolower($code) . ($squared ? ' fis' : '')]) }} role="img" aria-label="{{ strtoupper($code) }}"></span>
