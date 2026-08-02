<?php

namespace App\Services\Sites;

/**
 * Builds the URL set for a site's sitemap.xml (and gives the static build
 * command its canonical list of content URLs).
 */
class SitemapGenerator
{
    public function __construct(protected ContentRepository $content) {}

    /**
     * All indexable URLs for a site (every locale) with lastmod dates and
     * hreflang alternates. Rows are grouped by path: a document that exists
     * in several locales gets each locale's URL cross-linked via xhtml:link
     * (+ x-default). Translated posts have per-locale slugs, so they only
     * cross-link when the slugs match — which is the safe default.
     *
     * @return list<array{loc: string, lastmod: string|null, alternates: array<string, string>}>
     */
    public function urls(Site $site): array
    {
        $paths = [];

        foreach ($site->locales as $locale) {
            foreach ($this->localePaths($site, $locale) as $path => $lastmod) {
                $paths[$path][$locale] = $lastmod;
            }
        }

        $urls = [];

        foreach ($paths as $path => $locales) {
            $alternates = [];

            if (count($locales) > 1) {
                foreach (array_keys($locales) as $locale) {
                    $alternates[$locale] = $site->localizedUrl($path, $locale);
                }

                // x-default: the site's own locale when the document exists
                // in it, otherwise the first available locale.
                $alternates['x-default'] = $alternates[$site->locale] ?? reset($alternates);
            }

            foreach ($locales as $locale => $lastmod) {
                $urls[] = $this->row($site->localizedUrl($path, $locale), $lastmod, $alternates);
            }
        }

        return $urls;
    }

    /** @return array<string, string|null> path => lastmod */
    protected function localePaths(Site $site, string $locale): array
    {
        $paths = [];

        if ($this->homeExists($site, $locale)) {
            $paths['/'] = null;
        }

        foreach ($this->content->pages($site, locale: $locale) as $page) {
            if ($page['path'] === 'home') {
                continue;
            }

            $paths['/' . $page['path']] = $page['doc']->updated()?->toDateString();
        }

        $posts = $this->content->posts($site, locale: $locale);

        if ($posts->isNotEmpty()) {
            $paths['/blog'] = $posts->first()->updated()?->toDateString();

            foreach ($posts as $post) {
                $paths['/blog/' . $post->slug] = $post->updated()?->toDateString();
            }
        }

        return $paths;
    }

    /**
     * A Blade home view serves every locale; a markdown home only the
     * locales it is translated into. (File check, not view()->exists():
     * the site:: namespace is request-scoped and this also runs in
     * site:build outside any request.)
     */
    protected function homeExists(Site $site, string $locale): bool
    {
        return is_file($site->viewsPath() . '/pages/home.blade.php')
            || $this->content->page($site, 'home', locale: $locale) !== null;
    }

    /** @return array{loc: string, lastmod: string|null, alternates: array<string, string>} */
    protected function row(string $loc, ?string $lastmod, array $alternates = []): array
    {
        return ['loc' => $loc, 'lastmod' => $lastmod, 'alternates' => $alternates];
    }
}
