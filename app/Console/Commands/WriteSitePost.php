<?php

namespace App\Console\Commands;

use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/**
 * Drafts a full SEO article for a static site with AI (Anthropic Claude),
 * following the editorial skill in resources/sites/writing-guide.md (or the
 * site's own writing-guide.md override). Output is a markdown file with rich
 * front matter: tldr, faq, quiz, related, sources — all rendered by the blog
 * templates with matching JSON-LD.
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
                            {--publish : Create the post published instead of as a draft}
                            {--force : Overwrite an existing file with the same slug}';

    protected $description = 'Draft a full SEO article (TL;DR, TOC, FAQ, quiz, sources) as markdown for a static site';

    public function handle(SiteRegistry $registry, ContentRepository $content): int
    {
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
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('ANTHROPIC_API_KEY is not set (config/services.php).');
        }

        $prompt = $this->buildPrompt($site, $content, $topic, $slug);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(300)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model', 'claude-sonnet-4-20250514'),
            'max_tokens' => 16384,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Anthropic API error: ' . $response->body());
        }

        $text = $response->json('content.0.text', '');

        if (preg_match('/\{[\s\S]*\}/u', $text, $m)) {
            $text = $m[0];
        }

        $data = json_decode($text, true);

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

    protected function writingGuide(Site $site): string
    {
        $override = $site->path . '/writing-guide.md';
        $default = resource_path('sites/writing-guide.md');

        return File::exists($override)
            ? File::get($override)
            : (File::exists($default) ? File::get($default) : '');
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
