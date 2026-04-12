<?php

/**
 * SEO Configuration
 *
 * Site-wide defaults for all meta tags, structured data, and social sharing.
 * Per-page values override these via seo()->title('...') or @section('title').
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | The suffix is auto-appended to every page title via the modifier pattern.
    | Set 'suffix' to null to disable. The separator appears between title and suffix.
    |
    */
    'title' => [
        'default' => env('APP_NAME', 'My App'),
        'suffix' => env('APP_NAME', 'My App'),
        'separator' => ' | ',
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta Description
    |--------------------------------------------------------------------------
    */
    'description' => env('SEO_DESCRIPTION', 'Override this in config/seo.php or .env with SEO_DESCRIPTION'),

    /*
    |--------------------------------------------------------------------------
    | Default Keywords
    |--------------------------------------------------------------------------
    |
    | Comma-separated. Google ignores these but Yandex, Baidu, and Bing
    | may still use them as minor signals. Low cost, nonzero upside.
    |
    */
    'keywords' => env('SEO_KEYWORDS', ''),

    /*
    |--------------------------------------------------------------------------
    | Robots Defaults
    |--------------------------------------------------------------------------
    |
    | Global robots directives. Individual pages can override via seo()->robots('noindex, follow').
    | Common values: 'index, follow', 'noindex, follow', 'noindex, nofollow'
    | Additional directives: 'max-snippet:-1', 'max-image-preview:large', 'max-video-preview:-1'
    |
    */
    'robots' => env('SEO_ROBOTS', 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'),

    /*
    |--------------------------------------------------------------------------
    | Canonical URL
    |--------------------------------------------------------------------------
    |
    | When true, canonical and og:url are auto-set to the current URL (sans query string).
    | Pages can always override via seo()->canonical('https://...').
    |
    */
    'canonical' => true,

    /*
    |--------------------------------------------------------------------------
    | Open Graph
    |--------------------------------------------------------------------------
    */
    'og' => [
        'type' => 'website',
        'image' => env('SEO_IMAGE', ''),
        'image_width' => 1200,
        'image_height' => 630,
        'image_alt' => env('APP_NAME', 'My App'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Card
    |--------------------------------------------------------------------------
    |
    | 'site' is your brand's @handle. 'creator' is the default author handle.
    | Both can be overridden per-page via seo()->twitterSite('@other').
    |
    */
    'twitter' => [
        'card' => 'summary_large_image',
        'site' => env('TWITTER_SITE', ''),
        'creator' => env('TWITTER_CREATOR', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Organization (JSON-LD)
    |--------------------------------------------------------------------------
    |
    | Used for site-wide Organization schema. Establishes E-E-A-T signals.
    | sameAs links to your official social profiles help Google's Knowledge Panel.
    |
    */
    'organization' => [
        'name' => env('APP_NAME', 'My App'),
        'url' => env('APP_URL', 'https://example.com'),
        'logo' => env('SEO_ORG_LOGO', ''),
        'email' => env('SEO_ORG_EMAIL', ''),
        'phone' => env('SEO_ORG_PHONE', ''),
        'same_as' => array_filter([
            env('SOCIAL_FACEBOOK', ''),
            env('SOCIAL_TWITTER', ''),
            env('SOCIAL_INSTAGRAM', ''),
            env('SOCIAL_YOUTUBE', ''),
            env('SOCIAL_LINKEDIN', ''),
            env('SOCIAL_TIKTOK', ''),
            env('SOCIAL_GITHUB', ''),
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme & Branding
    |--------------------------------------------------------------------------
    */
    'theme_color' => env('SEO_THEME_COLOR', '#000000'),

    /*
    |--------------------------------------------------------------------------
    | Preconnect Domains
    |--------------------------------------------------------------------------
    |
    | Domains to preconnect to for faster resource loading.
    | Directly impacts LCP (Largest Contentful Paint) Core Web Vital.
    |
    */
    'preconnect' => array_filter([
        env('SEO_CDN_DOMAIN', ''),
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
    ]),

    /*
    |--------------------------------------------------------------------------
    | Feed / RSS
    |--------------------------------------------------------------------------
    */
    'feed' => [
        'enabled' => env('SEO_FEED_ENABLED', false),
        'url' => env('SEO_FEED_URL', '/feed'),
        'title' => env('SEO_FEED_TITLE', ''),
        'type' => 'application/rss+xml',
    ],

];
