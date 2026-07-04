<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use App\Services\Sites\ClusterRepository;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\MarkdownDocument;
use App\Services\Sites\Site;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        protected ContentRepository $content,
        protected SeoService $seoService,
        protected ClusterRepository $clusters,
    ) {}

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
                author: $post->author() ?? $site->author['name'] ?? null,
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

        $articleSchema = $this->seoService->getArticleSchema([
            'headline' => $post->title(),
            'url' => $url,
            'datePublished' => $post->date()?->toIso8601String() ?? '',
            'dateModified' => $post->updated()?->toIso8601String() ?? '',
            'author' => $post->author() ?? $site->author['name'] ?? $site->name,
            'image' => $post->image(),
            'articleSection' => $post->section(),
            'tags' => $post->tags(),
            'wordCount' => $post->wordCount(),
        ], 'BlogPosting');

        // E-E-A-T: a full Person (with sameAs social profiles) as the author
        // beats a bare name. Applies when the post is by the site author
        // (no author in front matter, or the same name).
        $bySiteAuthor = !empty($site->author['name'])
            && (!$post->author() || $post->author() === $site->author['name']);

        if ($bySiteAuthor && ($authorSchema = $site->authorSchema())) {
            $articleSchema['author'] = $authorSchema;
        }

        $schemas = [$articleSchema, $this->seoService->getBreadcrumbSchema($breadcrumbs)];

        if ($post->faq()) {
            $schemas[] = $this->seoService->getFaqPageSchema($post->faq());
        }

        return view('site::blog.show', [
            'post' => $post,
            'breadcrumbs' => $breadcrumbs,
            'schemas' => $schemas,
            'related' => $this->relatedPosts($site, $post),
            'bySiteAuthor' => $bySiteAuthor,
            'cluster' => $this->clusterContext($site, $post),
        ]);
    }

    /**
     * Topic cluster context for the template-enforced link boxes: a spoke
     * always links up to its pillar, a pillar always lists its published
     * spokes — even if the article body forgot the links (they are what
     * makes the cluster work as an SEO structure).
     */
    protected function clusterContext(Site $site, MarkdownDocument $post): ?array
    {
        if (!$post->cluster() || !($plan = $this->clusters->get($site, $post->cluster()))) {
            return null;
        }

        if ($this->clusters->isPillar($plan, $post->slug)) {
            $spokes = collect($plan['spokes'] ?? [])
                ->map(fn(array $spoke) => $this->content->post($site, $spoke['slug'] ?? ''))
                ->filter()
                ->values();

            return $spokes->isEmpty()
                ? null
                : ['role' => 'pillar', 'topic' => $plan['topic'] ?? $post->cluster(), 'spokes' => $spokes];
        }

        $pillar = $this->content->post($site, $plan['pillar']['slug'] ?? '');

        return $pillar === null
            ? null
            : ['role' => 'spoke', 'topic' => $plan['topic'] ?? $post->cluster(), 'pillar' => $pillar];
    }

    /**
     * Related articles: explicit `related:` slugs from front matter first,
     * topped up with the newest posts sharing a tag.
     */
    protected function relatedPosts(Site $site, $post, int $limit = 3)
    {
        $explicit = collect($post->related())
            ->map(fn(string $slug) => $this->content->post($site, $slug))
            ->filter();

        if ($explicit->count() >= $limit) {
            return $explicit->take($limit)->values();
        }

        $byTag = $this->content->posts($site)
            ->reject(fn($candidate) => $candidate->slug === $post->slug
                || $explicit->contains(fn($p) => $p->slug === $candidate->slug))
            ->filter(fn($candidate) => array_intersect($candidate->tags(), $post->tags()) !== []);

        return $explicit->concat($byTag)->take($limit)->values();
    }
}
