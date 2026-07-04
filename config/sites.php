<?php

/**
 * Static Sites (multi-domain)
 *
 * Opt-in: not every project built from this template is a static site hub.
 * When disabled, no site routes are registered and site:build is a no-op —
 * the resources/sites/ folder is inert.
 *
 * Enable with SITES_ENABLED=true, scaffold with `php artisan site:make`.
 */

return [

    'enabled' => env('SITES_ENABLED', false),

];
