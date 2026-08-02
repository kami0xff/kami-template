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


    //TODO this approach is fine for low number of pages but what about 1 or 2 million plus pages 
    //TODO we need a programatic way of doing things mabye like routing adding {username} {Category} {text} {emoji} {title app name}

    //todo investigate how sitemapas are generated for static sites 
    //investigate also how the robots.txt is generated 

    //TODO article and content_block trad id equivalent in the admin project add a section in the admin to manage them easily with ai generation + maybe api integraiton with wisewant or something like that

    //     articles — id, status (draft/published), published_at, author_id, cover_image, timestamps. No text here.
    // article_translations — article_id, locale, title, slug, excerpt, body, seo_title, seo_description, with unique(article_id, locale) and unique(locale, slug). Per-locale slugs are worth it (/es/blog/hola outranks /es/blog/hello); seo_title/seo_description are optional overrides that fall back to title/excerpt.
    // Non-article SEO texts (category intros, landing copy): a small content_blocks table in the admin (key, locale, body) — legitimate there because it's human-edited CMS content with an owner.

    //TODO monitor performance of data warehouse fetching 
    //it should be super fast they are literaly on the same network in the same country interconnected with the same fiber optics 
    //use http call pooling for fetching multiple ressources in parallel for rendering pages 
    //TCP TLS handshake disapears thanks to octane heyy that is a good thing 

    //caching views articles 
    //todo push invalidation from the data warehouse 
    //todo stale while revalidate 

    /*
    |--------------------------------------------------------------------------
    | Page Meta
    |--------------------------------------------------------------------------
    |
    | Per-page title / description / keywords / robots / image, applied with
    | seo()->page('home') in a view or controller. ":tokens" are replaced
    | from a data array: seo()->page('items.show', ['name' => $item->name]).
    | Values here override the global defaults above; anything set explicitly
    | via seo()->title(...) still wins over the pages map.
    |
    | Draft variants with: php artisan seo:generate-meta items.show
    |
    */
    'pages' => [
        'home' => [
            'title' => 'Hello World',
            'description' => 'Your new project starts here.',
        ],
        // 'items.show' => [
        //     'title' => ':name — specs, price and reviews',
        //     'description' => 'Everything about :name. Free shipping, 30-day returns.',
        // ],
    ],

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
