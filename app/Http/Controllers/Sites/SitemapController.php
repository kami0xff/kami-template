<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(protected ContentRepository $content)
    {
    }

    public function index(Site $site): Response
    {
        $posts = $this->content->posts($site);
        $pages = $this->content->pages($site);

        $urls = [
            ['loc' => $site->url('/'), 'lastmod' => null],
        ];

        foreach ($pages as $page) {
            if ($page['path'] === 'home') {
                continue;
            }
            $urls[] = [
                'loc' => $site->url('/' . $page['path']),
                'lastmod' => $page['doc']->updated()?->toDateString(),
            ];
        }

        if ($posts->isNotEmpty()) {
            $urls[] = [
                'loc' => $site->url('/blog'),
                'lastmod' => $posts->first()->updated()?->toDateString(),
            ];

            foreach ($posts as $post) {
                $urls[] = [
                    'loc' => $site->url('/blog/' . $post->slug),
                    'lastmod' => $post->updated()?->toDateString(),
                ];
            }
        }

        return response()
            ->view('site::sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
