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
     * All indexable URLs for a site with their lastmod dates.
     *
     * @return list<array{loc: string, lastmod: string|null}>
     */
    public function urls(Site $site): array
    {
        $urls = [$this->row($site->url('/'), null)];

        foreach ($this->content->pages($site) as $page) {
            if ($page['path'] === 'home') {
                continue;
            }

            $urls[] = $this->row(
                $site->url('/' . $page['path']),
                $page['doc']->updated()?->toDateString(),
            );
        }

        $posts = $this->content->posts($site);

        if ($posts->isNotEmpty()) {
            $urls[] = $this->row(
                $site->url('/blog'),
                $posts->first()->updated()?->toDateString(),
            );

            foreach ($posts as $post) {
                $urls[] = $this->row(
                    $site->url('/blog/' . $post->slug),
                    $post->updated()?->toDateString(),
                );
            }
        }

        return $urls;
    }

    /** @return array{loc: string, lastmod: string|null} */
    protected function row(string $loc, ?string $lastmod): array
    {
        return ['loc' => $loc, 'lastmod' => $lastmod];
    }
}
