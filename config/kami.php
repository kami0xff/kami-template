<?php

/**
 * Kami admin APIs (network, catalog, warehouse)
 *
 * The site talks to the admin from the server only: the URL and tokens below
 * never reach the browser. The contract lives in the admin project under
 * docs/frontends/ (README.md + network-api.md).
 *
 * The network client is off until both KAMI_ADMIN_URL and KAMI_SITE_TOKEN
 * are set; static-only projects simply leave them empty.
 */

return [

    // Base URL of the admin for THIS environment (dev admin in dev, prod in
    // prod), reachable from this server, e.g. https://admin-dev.example.com
    'admin_url' => env('KAMI_ADMIN_URL'),

    // This site's network token. Mint it in the admin project:
    //   php artisan network:site-token {pornbase|18fyp|feet-shop}
    'site_token' => env('KAMI_SITE_TOKEN'),

    // Only for projects that use the warehouse API.
    'warehouse_token' => env('KAMI_WAREHOUSE_TOKEN'),

    // Seconds per request.
    'timeout' => (int) env('KAMI_TIMEOUT', 10),

    // Http-only cookie holding the random per-browser id sent as X-Visitor-Id
    // (counts logged-out views once per browser).
    'visitor_cookie' => env('KAMI_VISITOR_COOKIE', 'kami_vid'),

    // Where a visitor whose account token died is sent to log in again.
    'login_path' => env('KAMI_LOGIN_PATH', '/login'),

];
