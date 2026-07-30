<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Caumont — Maison de Propriétés')</title>
    <meta name="description" content="@yield('meta_description', 'A private house for the stewardship and acquisition of remarkable properties.')">
    <meta name="theme-color" content="#0E0E10">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @stack('styles')
    @stack('head')
</head>
<body @yield('body_attrs')>
    @yield('content')
    @stack('scripts')
</body>
</html>
