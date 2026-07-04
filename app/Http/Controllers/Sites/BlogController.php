<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use App\Services\SeoService;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
        protected SeoService $seoService,
    ) {
    }

    public function index(Site $site): View
    {
        $posts = $this->content->posts($site, withDrafts: app()->environment('local'));

        seo()->title(__('Blog'))
            ->description(__('Articles and updates from :site', ['site' => $site->name]))
            ->canonical($site->url('/blog'));

        $schemas = [
            $this->seoService->getCollectionPageSchema(
                name: $site->name . ' Blog',
                url: $site->url('/blog'),
                numberOfItems: $posts->count(),
            ),
            $this->seoService->getItemListSchema(
                name: $site->name . ' Blog',
                totalItems: $posts->count(),
                items: $posts->map(fn($post) => [
                    'name' => $post->title(),
                    'url' => $site->url('/blog/' . $post->slug),
                ])->all(),
            ),
        ];

        return view('site::blog.index', [
            'posts' => $posts,
            'schemas' => $schemas,
        ]);
    }

    public function show(Site $site, string $slug): View
    {
        $post = $this->content->post($site, $slug, withDrafts: app()->environment('local'));

        abort_if($post === null, 404);

        $url = $site->url('/blog/' . $slug);

        seo()->title($post->title())
            ->description($post->description())
            ->canonical($url)
            ->article(
                publishedTime: $post->date()?->toIso8601String(),
                modifiedTime: $post->updated()?->toIso8601String(),
                author: $post->author(),
                section: $post->section(),
                tags: $post->tags(),
            );

        if ($post->image()) {
            seo()->image($post->image());
        }

        $breadcrumbs = [
            ['name' => $site->name, 'url' => $site->url('/')],
            ['name' => __('Blog'), 'url' => $site->url('/blog')],
            ['name' => $post->title(), 'url' => $url],
        ];

        $schemas = [
            $this->seoService->getArticleSchema([
                'headline' => $post->title(),
                'url' => $url,
                'datePublished' => $post->date()?->toIso8601String() ?? '',
                'dateModified' => $post->updated()?->toIso8601String() ?? '',
                'author' => $post->author() ?? $site->name,
                'image' => $post->image(),
                'articleSection' => $post->section(),
                'tags' => $post->tags(),
                'wordCount' => $post->wordCount(),
            ], 'BlogPosting'),
            $this->seoService->getBreadcrumbSchema($breadcrumbs),
        ];

        return view('site::blog.show', [
            'post' => $post,
            'breadcrumbs' => $breadcrumbs,
            'schemas' => $schemas,
        ]);
    }
}
