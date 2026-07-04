<?php

namespace App\Services\Sites;

use Illuminate\Support\Collection;

/**
 * Builds the URL set for a site's sitemap.xml (and gives the static build
 * command its canonical list of content URLs).
 */
class SitemapGenerator
{
    public function __construct(protected ContentRepository $content)
    {
    }

    /**
     * All indexable URLs for a site with their lastmod dates.
     *
     * @return Collection<int, array{loc: string, lastmod: string|null}>
     */
    public function urls(Site $site): Collection
    {
        $urls = collect([['loc' => $site->url('/'), 'lastmod' => null]]);

        foreach ($this->content->pages($site) as $page) {
            if ($page['path'] === 'home') {
                continue;
            }

            $urls->push([
                'loc' => $site->url('/' . $page['path']),
                'lastmod' => $page['doc']->updated()?->toDateString(),
            ]);
        }

        $posts = $this->content->posts($site);

        if ($posts->isNotEmpty()) {
            $urls->push([
                'loc' => $site->url('/blog'),
                'lastmod' => $posts->first()->updated()?->toDateString(),
            ]);

            foreach ($posts as $post) {
                $urls->push([
                    'loc' => $site->url('/blog/' . $post->slug),
                    'lastmod' => $post->updated()?->toDateString(),
                ]);
            }
        }

        return $urls;
    }
}
