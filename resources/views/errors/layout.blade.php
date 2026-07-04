{{--
    Minimal error page, used for the main app and every static site.

    When the request was scoped to a static site (SetSite shares $site with
    all views), the page shows that site's name and accent color — nothing
    else, so there is no hint that other sites live in the same project.
    Outside a site context it is just the status code, centered.
--}}
@php
    $site = $site ?? null;
    $accent = $site->seo['theme_color'] ?? '#2563eb';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') — @yield('message')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            color: #1f2937;
            text-align: center;
        }
        main { padding: 2rem; }
        .error-brand {
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 2rem;
        }
        .error-brand a { color: inherit; text-decoration: none; }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            margin: 0;
            color: {{ $accent }};
            letter-spacing: -0.03em;
        }
        .error-message {
            font-size: 1.125rem;
            color: #6b7280;
            margin: 1rem 0 2rem;
        }
        .error-home {
            display: inline-block;
            color: {{ $accent }};
            text-decoration: none;
            font-weight: 600;
            border: 1px solid currentColor;
            border-radius: 0.5rem;
            padding: 0.625rem 1.5rem;
        }
    </style>
</head>
<body>
    <main>
        @if($site)
            <p class="error-brand"><a href="/">{{ $site->name }}</a></p>
        @endif
        <p class="error-code">@yield('code')</p>
        <p class="error-message">@yield('message')</p>
        <a class="error-home" href="/">@yield('cta', __('Back to the home page'))</a>
    </main>
</body>
</html>
