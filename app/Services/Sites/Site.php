<?php

namespace App\Services\Sites;

/**
 * Immutable value object describing one static site hosted by this project.
 *
 * Built from a `resources/sites/{key}/site.php` config file. The first entry
 * in `domains` is the canonical domain; requests on any other listed domain
 * are 301-redirected to it.
 */
class Site
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly array $domains,
        public readonly string $locale,
        public readonly array $seo,
        public readonly string $path,
    ) {
    }

    public static function fromConfig(string $key, string $path, array $config): self
    {
        if (empty($config['domains'])) {
            throw new \InvalidArgumentException("Site [{$key}] must define at least one domain in site.php");
        }

        return new self(
            key: $key,
            name: $config['name'] ?? $key,
            domains: array_values($config['domains']),
            locale: $config['locale'] ?? 'en',
            seo: $config['seo'] ?? [],
            path: $path,
        );
    }

    public function canonicalDomain(): string
    {
        return $this->domains[0];
    }

    /**
     * Absolute URL on the canonical domain. Sites are always served over
     * HTTPS in production (TLS terminates at Cloudflare).
     */
    public function url(string $path = '/'): string
    {
        return 'https://' . $this->canonicalDomain() . '/' . ltrim($path, '/');
    }

    public function viewsPath(): string
    {
        return $this->path . '/views';
    }

    public function contentPath(string $sub = ''): string
    {
        return rtrim($this->path . '/content/' . ltrim($sub, '/'), '/');
    }

    /**
     * The final `seo` config for this site: template-wide defaults, then
     * site-derived defaults (name, canonical URL), then explicit overrides
     * from site.php.
     *
     * @param array $base Pristine config('seo') captured at boot, before any
     *                    request mutated it (important for Octane/site:build
     *                    where many requests share one process).
     */
    public function seoConfig(array $base): array
    {
        $siteDefaults = [
            'title' => [
                'default' => $this->name,
                'suffix' => $this->name,
            ],
            'og' => [
                'image_alt' => $this->name,
            ],
            'organization' => [
                'name' => $this->name,
                'url' => $this->url('/'),
            ],
            // Every site gets an RSS feed; layouts.app renders the
            // <link rel="alternate"> automatically from this.
            'feed' => [
                'enabled' => true,
                'url' => '/feed.xml',
                'title' => $this->name,
                'type' => 'application/rss+xml',
            ],
        ];

        return array_replace_recursive($base, $siteDefaults, $this->seo);
    }
}
