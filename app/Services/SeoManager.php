<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Str;

class SeoManager
{
    protected array $values = [];

    protected array $defaults = [];

    protected array $modifiers = [];

    protected array $tags = [];

    protected array $rawTags = [];

    /** Explicit hreflang map (locale => URL); null until set — then the
     *  hreflang() getter falls back to route-derived alternates. */
    protected ?array $hreflangUrls = null;

    public function __construct()
    {
        $this->loadConfigDefaults();
    }

    protected function loadConfigDefaults(): void
    {
        $c = config('seo', []);

        $this->defaults = [
            'title' => $c['title']['default'] ?? config('app.name'),
            'description' => $c['description'] ?? '',
            'keywords' => $c['keywords'] ?? '',
            'robots' => $c['robots'] ?? 'index, follow',
            'canonical' => null,
            'og_type' => $c['og']['type'] ?? 'website',
            'og_image' => $c['og']['image'] ?? '',
            'og_image_width' => $c['og']['image_width'] ?? 1200,
            'og_image_height' => $c['og']['image_height'] ?? 630,
            'og_image_alt' => $c['og']['image_alt'] ?? config('app.name'),
            'twitter_card' => $c['twitter']['card'] ?? 'summary_large_image',
            'twitter_site' => $c['twitter']['site'] ?? '',
            'twitter_creator' => $c['twitter']['creator'] ?? '',
        ];

        $suffix = $c['title']['suffix'] ?? null;
        $separator = $c['title']['separator'] ?? ' | ';

        if ($suffix) {
            $default = $this->defaults['title'];
            $this->modifiers['title'] = function (string $title) use ($suffix, $separator, $default) {
                if ($title === $default || Str::endsWith($title, $suffix)) {
                    return $title;
                }

                return $title . $separator . $suffix;
            };
        }

        foreach (array_filter($c['preconnect'] ?? []) as $domain) {
            $this->preconnect($domain);
        }
    }

    public function set(string $key, string|Closure|null $value): static
    {
        $this->values[$key] = $value;

        return $this;
    }

    public function get(string $key): ?string
    {
        $value = $this->values[$key] ?? $this->defaults[$key] ?? null;
        $resolved = $value instanceof Closure ? $value() : $value;

        if ($resolved !== null && isset($this->modifiers[$key])) {
            return $this->modifiers[$key]((string) $resolved);
        }

        return $resolved;
    }

    public function raw(string $key): ?string
    {
        $value = $this->values[$key] ?? $this->defaults[$key] ?? null;

        return $value instanceof Closure ? $value() : $value;
    }

    public function has(string $key): bool
    {
        return isset($this->values[$key]) || isset($this->defaults[$key]);
    }

    public function title(string|Closure|null $value = null, ?string $default = null, ?Closure $modify = null): static|string|null
    {
        return $this->property('title', $value, $default, $modify);
    }

    public function description(string|Closure|null $value = null, ?string $default = null, ?Closure $modify = null): static|string|null
    {
        return $this->property('description', $value, $default, $modify);
    }

    public function keywords(string|Closure|null $value = null): static|string|null
    {
        return $this->property('keywords', $value);
    }

    public function robots(string|Closure|null $value = null): static|string|null
    {
        return $this->property('robots', $value);
    }

    /**
     * noindex this page. Keeps "follow" by default so outgoing links still
     * pass equity — pass false for "noindex, nofollow" (e.g. thin utility
     * pages you don't want crawled at all).
     */
    public function noindex(bool $follow = true): static
    {
        return $this->robots($follow ? 'noindex, follow' : 'noindex, nofollow');
    }

    /**
     * hreflang alternates (locale => URL, incl. x-default). Set explicitly
     * for content pages whose slugs differ per locale — e.g. from an
     * article's translations:
     *
     *   seo()->hreflang(['en' => url('/blog/hello'), 'es' => url('/es/blog/hola'), 'x-default' => ...]);
     *
     * When never set, the getter derives alternates from the current named
     * route automatically, so every page in the localized route group gets
     * correct tags for free ([] in single-locale apps).
     */
    public function hreflang(?array $urls = null): static|array
    {
        if ($urls !== null) {
            $this->hreflangUrls = $urls;

            return $this;
        }

        return $this->hreflangUrls ?? $this->hreflangFromRoute();
    }

    /** @return array<string, string> */
    protected function hreflangFromRoute(): array
    {
        $route = request()->route();
        $name = $route?->getName();

        if (!$name) {
            return [];
        }

        // On a localized URL the route name carries the "localized." prefix;
        // routeAlternates() expects the base name.
        $base = str_starts_with($name, 'localized.') ? substr($name, strlen('localized.')) : $name;

        $params = $route->parameters();
        unset($params['locale']);

        return Sitemap::routeAlternates($base, $params);
    }

    public function canonical(string|Closure|null $value = null): static|string|null
    {
        return $this->property('canonical', $value);
    }

    public function image(string|Closure|null $url = null, ?int $width = null, ?int $height = null, ?string $alt = null): static|string|null
    {
        if ($url === null && $width === null) {
            return $this->get('og_image');
        }
        if ($url !== null) {
            $this->set('og_image', $url);
        }
        if ($width !== null) {
            $this->set('og_image_width', (string) $width);
        }
        if ($height !== null) {
            $this->set('og_image_height', (string) $height);
        }
        if ($alt !== null) {
            $this->set('og_image_alt', $alt);
        }

        return $this;
    }

    /**
     * Apply the config-driven meta for a page (config('seo.pages')[$key]).
     * ":tokens" are interpolated from $data:
     *
     *   seo()->page('items.show', ['name' => $item->name]);
     *
     * Call it before any page-specific overrides — later seo()->title(...)
     * calls still win.
     */
    public function page(string $key, array $data = []): static
    {
        $meta = (config('seo.pages', []))[$key] ?? null;

        if (!$meta) {
            return $this;
        }

        // Longest tokens first so ":name" can't eat the prefix of ":name_suffix".
        uksort($data, fn(string $a, string $b) => strlen($b) <=> strlen($a));

        $interpolate = function (?string $value) use ($data): ?string {
            if ($value === null) {
                return null;
            }
            foreach ($data as $token => $replacement) {
                $value = str_replace(':' . $token, (string) $replacement, $value);
            }

            return $value;
        };

        if ($title = $interpolate($meta['title'] ?? null)) {
            $this->title($title);
        }
        if ($description = $interpolate($meta['description'] ?? null)) {
            $this->description($description);
        }
        if (!empty($meta['keywords'])) {
            $this->keywords($interpolate($meta['keywords']));
        }
        if (!empty($meta['robots'])) {
            $this->robots($meta['robots']);
        }
        if (!empty($meta['image'])) {
            $this->image($meta['image']);
        }

        return $this;
    }

    public function type(string $type): static
    {
        return $this->set('og_type', $type);
    }

    public function twitterSite(string $handle): static
    {
        return $this->set('twitter_site', $handle);
    }

    public function twitterCreator(string $handle): static
    {
        return $this->set('twitter_creator', $handle);
    }

    public function twitterTitle(string $title): static
    {
        return $this->set('twitter_title', $title);
    }

    public function twitterDescription(string $desc): static
    {
        return $this->set('twitter_description', $desc);
    }

    public function twitterImage(string $url): static
    {
        return $this->set('twitter_image', $url);
    }

    public function article(
        ?string $publishedTime = null,
        ?string $modifiedTime = null,
        ?string $author = null,
        ?string $section = null,
        array $tags = [],
    ): static {
        $this->set('og_type', 'article');
        if ($publishedTime) {
            $this->set('article_published_time', $publishedTime);
        }
        if ($modifiedTime) {
            $this->set('article_modified_time', $modifiedTime);
        }
        if ($author) {
            $this->set('article_author', $author);
        }
        if ($section) {
            $this->set('article_section', $section);
        }
        if (!empty($tags)) {
            $this->set('article_tags', implode(',', $tags));
        }

        return $this;
    }

    public function tag(string $property, string $content): static
    {
        $this->tags[$property] = e($content);

        return $this;
    }

    public function rawTag(string $keyOrHtml, ?string $html = null): static
    {
        if ($html === null) {
            $this->rawTags[md5($keyOrHtml)] = $keyOrHtml;
        } else {
            $this->rawTags[$keyOrHtml] = $html;
        }

        return $this;
    }

    public function preconnect(string $url): static
    {
        return $this->rawTag(
            "preconnect:{$url}",
            '<link rel="preconnect" href="' . e($url) . '">'
        );
    }

    public function dnsPrefetch(string $url): static
    {
        return $this->rawTag(
            "dns-prefetch:{$url}",
            '<link rel="dns-prefetch" href="' . e($url) . '">'
        );
    }

    public function preload(string $url, string $as, ?string $type = null): static
    {
        $tag = '<link rel="preload" href="' . e($url) . '" as="' . e($as) . '"';
        if ($type) {
            $tag .= ' type="' . e($type) . '"';
        }
        $tag .= '>';

        return $this->rawTag("preload:{$url}", $tag);
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getRawTags(): array
    {
        return $this->rawTags;
    }

    /**
     * Resolve a value: manager first, then Blade @yield fallback.
     * Backward compatible with the existing @section/@yield pattern.
     */
    public function resolve(string $key, ?string $bladeFallback = null): ?string
    {
        if (isset($this->values[$key])) {
            return $this->get($key);
        }

        if ($bladeFallback !== null && $bladeFallback !== '') {
            if (isset($this->modifiers[$key])) {
                return $this->modifiers[$key]($bladeFallback);
            }

            return $bladeFallback;
        }

        return $this->get($key);
    }

    public function getArticleTags(): array
    {
        $result = [];
        $map = [
            'article_published_time' => 'article:published_time',
            'article_modified_time' => 'article:modified_time',
            'article_author' => 'article:author',
            'article_section' => 'article:section',
        ];

        foreach ($map as $key => $ogProp) {
            $val = $this->get($key);
            if ($val) {
                $result[$ogProp] = $val;
            }
        }

        $tagString = $this->get('article_tags');
        if ($tagString) {
            $result['article:tag'] = array_map('trim', explode(',', $tagString));
        }

        return $result;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->get('title'),
            'description' => $this->get('description'),
            'keywords' => $this->get('keywords'),
            'robots' => $this->get('robots'),
            'canonical' => $this->get('canonical') ?? request()->url(),
            'og_type' => $this->get('og_type'),
            'og_image' => $this->get('og_image'),
            'og_image_width' => $this->get('og_image_width'),
            'og_image_height' => $this->get('og_image_height'),
            'og_image_alt' => $this->get('og_image_alt'),
            'twitter_card' => $this->get('twitter_card'),
            'twitter_site' => $this->get('twitter_site'),
            'twitter_creator' => $this->get('twitter_creator'),
        ];
    }

    protected function property(
        string $key,
        string|Closure|null $value = null,
        ?string $default = null,
        ?Closure $modify = null,
    ): static|string|null {
        if ($default !== null) {
            $this->defaults[$key] = $default;
        }
        if ($modify !== null) {
            $this->modifiers[$key] = $modify;
        }
        if ($value !== null) {
            $this->set($key, $value);

            return $this;
        }
        if ($default !== null || $modify !== null) {
            return $this;
        }

        return $this->get($key);
    }
}
