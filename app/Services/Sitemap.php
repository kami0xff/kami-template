<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Route;

/**
 * Registry for the main app's sitemap.xml. Sources are closures returning
 * entries shaped:
 *
 *   ['loc' => string, 'lastmod' => ?string, 'alternates' => [locale => url]]
 *
 * Register them in AppServiceProvider::boot() (closures — nothing runs per
 * request) and write the file on deploy with `php artisan seo:sitemap`.
 * Google caps a sitemap at 50k URLs / 50MB — split into a sitemap index
 * when a project outgrows that.
 */
class Sitemap
{
    /** @var array<string, Closure(): iterable> */
    protected array $sources = [];

    public function add(string $key, Closure $source): static
    {
        $this->sources[$key] = $source;

        return $this;
    }

    /**
     * @return list<array{loc: string, lastmod: string|null, alternates: array<string, string>}>
     */
    public function urls(): array
    {
        $urls = [];

        foreach ($this->sources as $source) {
            foreach ($source() as $entry) {
                $urls[] = $entry + ['lastmod' => null, 'alternates' => []];
            }
        }

        return $urls;
    }

    /** @return array{loc: string, lastmod: string|null, alternates: array<string, string>} */
    public static function entry(string $loc, ?string $lastmod = null, array $alternates = []): array
    {
        return ['loc' => $loc, 'lastmod' => $lastmod, 'alternates' => $alternates];
    }

    /**
     * Entry for a named route: loc is the default-locale URL, alternates
     * cover every supported locale (+ x-default) when the localized route
     * group is registered.
     *
     *   Sitemap::forRoute('items.show', ['slug' => $item->slug], $item->updated_at?->toDateString());
     *
     * @return array{loc: string, lastmod: string|null, alternates: array<string, string>}
     */
    public static function forRoute(string $name, array $params = [], ?string $lastmod = null): array
    {
        return static::entry(route($name, $params), $lastmod, static::routeAlternates($name, $params));
    }

    /**
     * locale => URL map for a named route across all supported locales,
     * with x-default pointing at the default locale. Empty when the route
     * has no localized variant (single-locale apps).
     *
     * @return array<string, string>
     */
    public static function routeAlternates(string $name, array $params = []): array
    {
        if (!Route::has("localized.{$name}")) {
            return [];
        }

        $default = config('locales.default', 'en');
        $urls = [];

        foreach (array_keys(config('locales.supported', [])) as $locale) {
            $urls[$locale] = $locale === $default
                ? route($name, $params)
                : route("localized.{$name}", ['locale' => $locale] + $params);
        }

        $urls['x-default'] = $urls[$default] ?? reset($urls);

        return $urls;
    }
}
