<?php

namespace App\Console\Commands;

use App\Services\Anthropic;
use App\Services\Sites\ClusterRepository;
use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/**
 * Drafts a full SEO article for a static site with AI (Anthropic Claude),
 * following an editorial skill from resources/skills/ (default: seo-article,
 * pick another with --skill, per-site overrides in the site's skills/ dir).
 * Output is a markdown file with rich front matter: tldr, faq, quiz, related,
 * sources — all rendered by the blog templates with matching JSON-LD.
 *
 *   php artisan site:write example "How to choose a standing desk"
 *   php artisan site:write example "..." --keywords="standing desk, ergonomics" --words=1500
 *
 * Posts are created with `draft: true` — review, drop in the screenshot
 * placeholders, then remove the flag (or pass --publish).
 */
class WriteSitePost extends Command
{
    protected $signature = 'site:write
                            {site : Site key under resources/sites/}
                            {topic : What the article should be about}
                            {--slug= : URL slug (defaults to a slug of the topic)}
                            {--keywords= : Comma-separated target keywords to work in naturally}
                            {--words=1200 : Approximate target word count}
                            {--author= : Author for the front matter (defaults to the site author)}
                            {--skill=seo-article : Editorial skill from resources/skills/{skill}.md}
                            {--cluster= : Topic cluster this article belongs to (see site:cluster)}
                            {--publish : Create the post published instead of as a draft}
                            {--force : Overwrite an existing file with the same slug}';

    protected $description = 'Draft a full SEO article (TL;DR, TOC, FAQ, quiz, sources) as markdown for a static site';

    protected Anthropic $anthropic;

    protected ClusterRepository $clusters;

    public function handle(
        SiteRegistry $registry,
        ContentRepository $content,
        Anthropic $anthropic,
        ClusterRepository $clusters,
    ): int {
        $this->anthropic = $anthropic;
        $this->clusters = $clusters;

        $site = $registry->get($this->argument('site'));

        if ($site === null) {
            $this->components->error("Site [{$this->argument('site')}] not found under resources/sites/.");

            return self::FAILURE;
        }

        $topic = $this->argument('topic');
        $slug = Str::slug($this->option('slug') ?: $topic);
        $file = $site->contentPath('blog') . "/{$slug}.md";

        if (File::exists($file) && !$this->option('force')) {
            $this->components->error("Post [{$slug}] already exists. Use --force to overwrite.");

            return self::FAILURE;
        }

        $this->components->info("Drafting \"{$topic}\" for [{$site->key}]...");

        try {
            $data = $this->generate($site, $content, $topic, $slug);
        } catch (\Exception $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($file));
        File::put($file, $this->toMarkdown($site, $data));

        $this->components->info('Created ' . str_replace(base_path() . '/', '', $file));
        $this->components->bulletList(array_filter([
            'Replace the image placeholders (search the file for "TODO screenshot")',
            'Add manual in-text links where you want them',
            $this->option('publish') ? null : 'Review the draft, then remove `draft: true` to publish',
            "Preview: /blog/{$slug} (drafts render in local dev)",
            "Rebuild: php artisan site:build {$site->key}",
        ]));

        return self::SUCCESS;
    }

    protected function generate(Site $site, ContentRepository $content, string $topic, string $slug): array
    {
        $data = $this->anthropic->completeJson($this->buildPrompt($site, $content, $topic, $slug));

        if (empty($data['markdown'])) {
            throw new \RuntimeException('AI response did not contain article markdown.');
        }

        return $data;
    }

    protected function buildPrompt(Site $site, ContentRepository $content, string $topic, string $slug): string
    {
        $guide = $this->writingGuide($site);
        $words = (int) $this->option('words');
        $keywords = $this->option('keywords');

        $keywordLine = $keywords
            ? "Target keywords (work in naturally, no stuffing): {$keywords}"
            : 'Choose the primary and secondary keywords yourself based on the topic.';

        $authorContext = !empty($site->author['name'])
            ? "The author is {$site->author['name']}" .
              (!empty($site->author['title']) ? " ({$site->author['title']})" : '') .
              (!empty($site->author['bio']) ? ": {$site->author['bio']}" : '.') .
              ' Write from their first-person practitioner perspective.'
            : 'Write from a hands-on practitioner perspective.';

        $existing = $content->posts($site)
            ->take(30)
            ->map(fn($post) => "- /blog/{$post->slug} — \"{$post->title()}\" [" . implode(', ', $post->tags()) . ']')
            ->implode("\n");

        $existingBlock = $existing
            ? "Existing articles on this site (link to them in the body where genuinely relevant, and pick `related` slugs from this list ONLY):\n{$existing}"
            : 'This site has no other articles yet — leave `related` empty and use no internal links.';

        $clusterBlock = $this->clusterBlock($site, $slug);

        return <<<PROMPT
        You are an elite SEO content writer. Follow this editorial guide exactly:

        <writing_guide>
        {$guide}
        </writing_guide>

        <site_context>
        Site: "{$site->name}" — {$site->url('/')}
        Site description: {$this->siteDescription($site)}
        {$authorContext}
        {$existingBlock}
        </site_context>
        {$clusterBlock}
        <assignment>
        Write an article about: {$topic}
        URL slug (already decided): /blog/{$slug}
        {$keywordLine}
        Length: around {$words} words of body markdown.
        </assignment>

        Respond with ONLY this JSON (no text outside it):
        {
            "title": "SEO title, under 60 characters, includes primary keyword",
            "description": "Meta description, 150-160 characters, includes primary keyword and a reason to click",
            "section": "One short category name",
            "tags": ["lowercase-tag", "..."],
            "tldr": ["3-5 takeaway bullets, each a complete standalone sentence"],
            "markdown": "Full article body per the writing guide: # H1, direct answer, [TOC] placeholder, question H2s with answer-first paragraphs, data tables with linked sources, image placeholders with TODO comments, internal links to existing articles, authority links, and a Conclusion H2 with one CTA.",
            "faq": [{"question": "Long-tail question", "answer": "40-80 word self-contained answer"}],
            "quiz": {"question": "One question testing the article's core decision", "options": ["...", "...", "..."], "answer": 0, "explanation": "Why that option is right"},
            "related": ["existing-slug-1", "existing-slug-2"],
            "sources": [{"title": "Source name", "url": "https://authoritative-source"}]
        }
        PROMPT;
    }

    /**
     * Hub-and-spoke instructions when the article belongs to a topic cluster
     * (see site:cluster). Spokes must link up to the pillar; the pillar must
     * link down to every drafted spoke. Only drafted articles are linkable —
     * in-body links to planned-but-unwritten slugs would 404.
     */
    protected function clusterBlock(Site $site, string $slug): string
    {
        $name = $this->option('cluster');

        if (!$name) {
            return '';
        }

        $plan = $this->clusters->get($site, $name);

        if (!$plan) {
            throw new \RuntimeException("Cluster [{$name}] not found — plan it first: php artisan site:cluster {$site->key} \"topic\"");
        }

        $pillar = $plan['pillar'] ?? [];
        $spokeLine = fn(array $s) => "- /blog/{$s['slug']} — \"{$s['title']}\" (keyword: {$s['keyword']})";

        $drafted = collect($plan['spokes'] ?? [])
            ->where('status', 'drafted')
            ->reject(fn($s) => $s['slug'] === $slug)
            ->map($spokeLine)
            ->implode("\n");

        if ($this->clusters->isPillar($plan, $slug)) {
            $links = $drafted ?: '(none drafted yet — use no spoke links)';

            return <<<TEXT

            <topic_cluster>
            This article is the PILLAR page of the "{$plan['topic']}" topic cluster.
            It must comprehensively cover the head keyword "{$pillar['keyword']}" and act as the hub:
            link to EACH of these spoke articles from the section where its subtopic comes up,
            with descriptive keyword-rich anchor text (not "click here"):
            {$links}
            </topic_cluster>

            TEXT;
        }

        $siblings = $drafted ?: '(none drafted yet)';

        return <<<TEXT

        <topic_cluster>
        This article is a SPOKE in the "{$plan['topic']}" topic cluster.
        The pillar page is /blog/{$pillar['slug']} — "{$pillar['title']}".
        Link to the pillar ONCE early in the article (first or second section) with anchor
        text containing "{$pillar['keyword']}", and once more in the conclusion.
        Sibling spokes already published (link where genuinely relevant):
        {$siblings}
        </topic_cluster>

        TEXT;
    }

    /**
     * Resolve the editorial skill: the site's own override wins, then the
     * app-level skill in resources/skills/.
     */
    protected function writingGuide(Site $site): string
    {
        $skill = basename($this->option('skill'));

        foreach ([
            $site->path . "/skills/{$skill}.md",
            resource_path("skills/{$skill}.md"),
        ] as $file) {
            if (File::exists($file)) {
                return File::get($file);
            }
        }

        throw new \RuntimeException("Skill [{$skill}] not found in resources/skills/.");
    }

    protected function siteDescription(Site $site): string
    {
        return $site->seo['description'] ?? config('seo.description', '');
    }

    protected function toMarkdown(Site $site, array $data): string
    {
        $matter = array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'date' => now()->toDateString(),
            'author' => $this->option('author') ?: ($site->author['name'] ?? null),
            'section' => $data['section'] ?? null,
            'tags' => $data['tags'] ?? null,
            'cluster' => $this->option('cluster') ?: null,
            'tldr' => $data['tldr'] ?? null,
            'faq' => $data['faq'] ?? null,
            'quiz' => $data['quiz'] ?? null,
            'related' => $data['related'] ?? null,
            'sources' => $data['sources'] ?? null,
            'draft' => $this->option('publish') ? null : true,
        ]);

        $yaml = trim(Yaml::dump($matter, 4, 2));

        return "---\n{$yaml}\n---\n\n" . trim($data['markdown']) . "\n";
    }
}
