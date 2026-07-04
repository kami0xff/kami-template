<?php

namespace App\Services\Sites;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Reads markdown content for a site from `resources/sites/{key}/content/`:
 *
 *   content/blog/{slug}.md    -> /blog/{slug}   (post; filename is the slug)
 *   content/pages/{path}.md   -> /{path}        (static page, may be nested)
 *
 * Multi-locale sites ('locales' in site.php) keep the default locale at the
 * content root and nest each extra locale in its own subtree, served under
 * a /{locale} URL prefix:
 *
 *   content/es/blog/{slug}.md -> /es/blog/{slug}
 *   content/es/pages/home.md  -> /es/
 *
 * The same slug across locales marks documents as translations of each
 * other (that is what links them in hreflang alternates). Lookups default
 * to the current app locale, which the SetSite middleware derives from the
 * URL prefix.
 *
 * Rendered HTML is cached keyed by file mtime, so editing a file busts the
 * cache automatically.
 */
class ContentRepository
{
    protected ?MarkdownConverter $converter = null;

    public function __construct(protected ImageProcessor $images) {}

    /**
     * All blog posts for a site, newest first. Drafts are excluded unless
     * explicitly requested (the local environment requests them so you can
     * preview a draft before publishing).
     *
     * @return Collection<int, MarkdownDocument>
     */
    public function posts(Site $site, bool $withDrafts = false, ?string $locale = null): Collection
    {
        return collect(glob($this->dir($site, 'blog', $locale) . '/*.md') ?: [])
            ->map(fn(string $file) => $this->parse($site, $file))
            ->filter(fn(MarkdownDocument $doc) => $withDrafts || !$doc->isDraft())
            ->sortByDesc(fn(MarkdownDocument $doc) => $doc->date()?->getTimestamp() ?? 0)
            ->values();
    }

    public function post(Site $site, string $slug, bool $withDrafts = false, ?string $locale = null): ?MarkdownDocument
    {
        $file = $this->dir($site, 'blog', $locale) . '/' . $slug . '.md';

        if (!$this->isSafeContentFile($site, $file)) {
            return null;
        }

        $doc = $this->parse($site, $file);

        return (!$withDrafts && $doc->isDraft()) ? null : $doc;
    }

    /**
     * A markdown static page, e.g. path "about" or "guides/getting-started".
     */
    public function page(Site $site, string $path, bool $withDrafts = false, ?string $locale = null): ?MarkdownDocument
    {
        $file = $this->dir($site, 'pages', $locale) . '/' . $path . '.md';

        if (!$this->isSafeContentFile($site, $file)) {
            return null;
        }

        $doc = $this->parse($site, $file);

        return (!$withDrafts && $doc->isDraft()) ? null : $doc;
    }

    /**
     * All markdown static pages of a site (used by sitemap + static build).
     *
     * @return Collection<int, array{path: string, doc: MarkdownDocument}>
     */
    public function pages(Site $site, bool $withDrafts = false, ?string $locale = null): Collection
    {
        $base = $this->dir($site, 'pages', $locale);

        return collect($this->globRecursive($base . '/*.md'))
            ->map(fn(string $file) => [
                // Slug for pages is the path relative to content/pages, no extension.
                'path' => substr($file, strlen($base) + 1, -3),
                'doc' => $this->parse($site, $file),
            ])
            ->filter(fn(array $item) => $withDrafts || !$item['doc']->isDraft())
            ->values();
    }

    /**
     * Locales in which a blog post exists (same slug = translation).
     *
     * @return string[]
     */
    public function postLocales(Site $site, string $slug): array
    {
        return array_values(array_filter(
            $site->locales,
            fn(string $locale) => $this->post($site, $slug, locale: $locale) !== null,
        ));
    }

    /**
     * Locales in which a markdown page exists (same path = translation).
     *
     * @return string[]
     */
    public function pageLocales(Site $site, string $path): array
    {
        return array_values(array_filter(
            $site->locales,
            fn(string $locale) => $this->page($site, $path, locale: $locale) !== null,
        ));
    }

    /**
     * Content directory for a locale: the default locale lives at the
     * content root, extra locales in their own subtree (content/{locale}/).
     */
    protected function dir(Site $site, string $sub, ?string $locale): string
    {
        $locale ??= app()->getLocale();

        $prefix = $site->isMultiLocale()
            && $locale !== $site->locale
            && in_array($locale, $site->locales, true)
            ? $locale . '/'
            : '';

        return $site->contentPath($prefix . $sub);
    }

    protected function parse(Site $site, string $file): MarkdownDocument
    {
        $mtime = (int) filemtime($file);
        $slug = basename($file, '.md');
        $key = "site:{$site->key}:md:" . md5($file) . ":{$mtime}";

        [$matter, $html] = Cache::remember($key, now()->addDay(), function () use ($site, $file) {
            $result = $this->converter()->convert((string) file_get_contents($file));

            $matter = $result instanceof RenderedContentWithFrontMatter
                ? (array) $result->getFrontMatter()
                : [];

            // /images/ tags get WebP srcset + intrinsic dimensions + lazy
            // loading. (Cached with the content: replacing an image with one
            // of different dimensions needs a content touch or cache clear.)
            return [$matter, $this->images->transformHtml($site, $result->getContent())];
        });

        return new MarkdownDocument($slug, $matter, $html, $mtime);
    }

    protected function converter(): MarkdownConverter
    {
        if ($this->converter === null) {
            $environment = new Environment([
                // Content is author-owned, so inline HTML is allowed — but
                // javascript:/data: links are always stripped.
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
                // Every heading gets an id + anchor link (jump links can
                // surface as sitelinks in search results).
                'heading_permalink' => [
                    'insert' => 'after',
                    'symbol' => '#',
                    'aria_hidden' => true,
                    'min_heading_level' => 2,
                    'max_heading_level' => 3,
                    'id_prefix' => '',
                    'fragment_prefix' => '',
                ],
                // A linked table of contents renders wherever a [TOC]
                // placeholder appears in the markdown.
                'table_of_contents' => [
                    'position' => 'placeholder',
                    'placeholder' => '[TOC]',
                    'min_heading_level' => 2,
                    'max_heading_level' => 3,
                    'html_class' => 'toc',
                ],
            ]);
            $environment->addExtension(new CommonMarkCoreExtension);
            $environment->addExtension(new GithubFlavoredMarkdownExtension);
            $environment->addExtension(new FrontMatterExtension);
            $environment->addExtension(new HeadingPermalinkExtension);
            $environment->addExtension(new TableOfContentsExtension);

            $this->converter = new MarkdownConverter($environment);
        }

        return $this->converter;
    }

    /**
     * Route patterns already restrict paths to [a-z0-9\-_/], but content
     * lookups must never escape the site's content directory regardless of
     * how they are invoked.
     */
    protected function isSafeContentFile(Site $site, string $file): bool
    {
        $real = realpath($file);

        return $real !== false
            && str_starts_with($real, realpath($site->contentPath()) . DIRECTORY_SEPARATOR);
    }

    /** @return string[] */
    protected function globRecursive(string $pattern): array
    {
        $files = glob($pattern) ?: [];

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
            $files = array_merge($files, $this->globRecursive($dir . '/' . basename($pattern)));
        }

        return $files;
    }
}
