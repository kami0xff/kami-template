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
     * All indexable URLs for a site (every locale) with their lastmod dates.
     *
     * @return list<array{loc: string, lastmod: string|null}>
     */
    public function urls(Site $site): array
    {
        $urls = [];

        foreach ($site->locales as $locale) {
            $urls = array_merge($urls, $this->localeUrls($site, $locale));
        }

        return $urls;
    }

    /** @return list<array{loc: string, lastmod: string|null}> */
    protected function localeUrls(Site $site, string $locale): array
    {
        $urls = [];

        if ($this->homeExists($site, $locale)) {
            $urls[] = $this->row($site->localizedUrl('/', $locale), null);
        }

        foreach ($this->content->pages($site, locale: $locale) as $page) {
            if ($page['path'] === 'home') {
                continue;
            }

            $urls[] = $this->row(
                $site->localizedUrl('/' . $page['path'], $locale),
                $page['doc']->updated()?->toDateString(),
            );
        }

        $posts = $this->content->posts($site, locale: $locale);

        if ($posts->isNotEmpty()) {
            $urls[] = $this->row(
                $site->localizedUrl('/blog', $locale),
                $posts->first()->updated()?->toDateString(),
            );

            foreach ($posts as $post) {
                $urls[] = $this->row(
                    $site->localizedUrl('/blog/' . $post->slug, $locale),
                    $post->updated()?->toDateString(),
                );
            }
        }

        return $urls;
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

    /** @return array{loc: string, lastmod: string|null} */
    protected function row(string $loc, ?string $lastmod): array
    {
        return ['loc' => $loc, 'lastmod' => $lastmod];
    }
}
