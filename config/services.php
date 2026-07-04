<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    // OpenReplay session recording. Off by default — a project key alone
    // is not enough, recording must be explicitly enabled per project.
    'openreplay' => [
        'enabled' => env('OPENREPLAY_ENABLED', false),
        'project_key' => env('OPENREPLAY_PROJECT_KEY'),
    ],

    // Umami (privacy-friendly, cookieless analytics) for the MAIN app.
    // Static sites configure their own website_id per site in site.php
    // ('analytics' block) so each site is a separate Umami website.
    'umami' => [
        'src' => env('UMAMI_SRC', 'https://cloud.umami.is/script.js'),
        'website_id' => env('UMAMI_WEBSITE_ID'),
    ],

    // Used by AI content commands (`seo:generate-page-content`, `site:write`).
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
    ],

];
