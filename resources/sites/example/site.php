<?php

/**
 * Example site — reference for the multi-site static setup.
 *
 * The feature tests use this site (domain example.test), so keep it if you
 * want the tests to pass, or update the tests when you remove it. It never
 * responds in production unless you actually point example.test at the server.
 *
 * Create your own site with:  php artisan site:make mysite mysite.com
 */
return [
    // Display name — used as the title suffix, og:site_name, and in schema.
    'name' => 'Example Site',

    // First domain is canonical; the rest 301-redirect to it.
    'domains' => ['example.test', 'www.example.test'],

    'locale' => 'en',

    // Optional: override any key from config/seo.php for this site.
    'seo' => [
        'description' => 'An example static site demonstrating markdown content, blog posts, and per-site SEO.',
        'theme_color' => '#2563eb',
        'organization' => [
            'logo' => 'https://example.test/logo.png',
        ],
    ],
];
