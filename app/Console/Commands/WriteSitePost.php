<?php

namespace App\Console\Commands;

use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Drafts a blog post for a static site with AI (Anthropic Claude) and writes
 * it as a markdown file with full SEO front matter:
 *
 *   php artisan site:write example "How to choose a standing desk"
 *   php artisan site:write example "..." --keywords="standing desk, ergonomics" --words=1200
 *
 * Posts are created with `draft: true` — review, edit, then remove the flag
 * (or pass --publish to skip the draft stage).
 */
class WriteSitePost extends Command
{
    protected $signature = 'site:write
                            {site : Site key under resources/sites/}
                            {topic : What the article should be about}
                            {--slug= : URL slug (defaults to a slug of the topic)}
                            {--keywords= : Comma-separated target keywords to work in naturally}
                            {--words=900 : Approximate target word count}
                            {--author= : Author name for the front matter}
                            {--publish : Create the post published instead of as a draft}
                            {--force : Overwrite an existing file with the same slug}';

    protected $description = 'Draft a blog post for a static site using AI, saved as markdown with SEO front matter';

    public function handle(SiteRegistry $registry): int
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
            $data = $this->generate($site->name, $topic);
        } catch (\Exception $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($file));
        File::put($file, $this->toMarkdown($data));

        $this->components->info('Created ' . str_replace(base_path() . '/', '', $file));
        $this->components->bulletList(array_filter([
            $this->option('publish') ? null : 'Review the draft, then remove `draft: true` to publish',
            "Preview: /blog/{$slug} (drafts render in local dev)",
            "Rebuild: php artisan site:build {$site->key}",
        ]));

        return self::SUCCESS;
    }

    protected function generate(string $siteName, string $topic): array
    {
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('ANTHROPIC_API_KEY is not set (config/services.php).');
        }

        $words = (int) $this->option('words');
        $keywords = $this->option('keywords');

        $keywordLine = $keywords
            ? "Work these target keywords in naturally (no stuffing): {$keywords}."
            : 'Choose 3-5 sensible target keywords yourself.';

        $prompt = <<<PROMPT
        You are an expert SEO content writer for the website "{$siteName}".

        Write a blog article about: {$topic}

        Requirements:
        - Around {$words} words of genuinely useful, specific content — no filler.
        - {$keywordLine}
        - Use markdown: one # H1, several ## H2 sections, lists/tables where helpful.
        - Write for humans first; search engines second.

        Respond in this exact JSON format (no text outside the JSON):
        {
            "title": "SEO title, under 60 characters",
            "description": "Meta description, 150-160 characters",
            "section": "One short category name",
            "tags": ["tag1", "tag2", "tag3"],
            "markdown": "The full article body in markdown, starting with the # H1"
        }
        PROMPT;

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(180)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model', 'claude-sonnet-4-20250514'),
            'max_tokens' => 8192,
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

    protected function toMarkdown(array $data): string
    {
        $matter = array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'date' => now()->toDateString(),
            'author' => $this->option('author'),
            'section' => $data['section'] ?? null,
            'tags' => $data['tags'] ?? null,
            'draft' => $this->option('publish') ? null : true,
        ]);

        $yaml = collect($matter)->map(function ($value, $key) {
            if (is_array($value)) {
                return $key . ': [' . implode(', ', $value) . ']';
            }
            if (is_bool($value)) {
                return $key . ': ' . ($value ? 'true' : 'false');
            }

            return $key . ': ' . (preg_match('/[:#\[\]{}]/', (string) $value)
                ? '"' . str_replace('"', '\"', $value) . '"'
                : $value);
        })->implode("\n");

        return "---\n{$yaml}\n---\n\n" . trim($data['markdown']) . "\n";
    }
}
