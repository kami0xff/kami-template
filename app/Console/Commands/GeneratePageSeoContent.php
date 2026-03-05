<?php

namespace App\Console\Commands;

use App\Models\PageSeoContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GeneratePageSeoContent extends Command
{
    protected $signature = 'seo:generate-page-content
                            {--pages=* : Specific page keys to generate}
                            {--locale=en : Locale to generate content for}
                            {--translate : Translate existing English content to other locales}
                            {--priority : Use priority locales only}
                            {--position=bottom : Position of content (top or bottom)}
                            {--force : Overwrite existing content}';

    protected $description = 'Generate SEO text content for pages using AI (Anthropic Claude)';

    public function handle()
    {
        $position = $this->option('position');
        $force = $this->option('force');
        $translate = $this->option('translate');

        $locales = $this->getTargetLocales();
        $this->info("Generating SEO content for " . count($locales) . " locale(s)");

        $pages = $this->option('pages');
        if (empty($pages)) {
            $pages = ['home'];
        }

        foreach ($locales as $locale) {
            $this->newLine();
            $this->info("Processing locale: {$locale}");

            if ($translate && $locale !== 'en') {
                $this->translateExistingContent($locale, $force);
                continue;
            }

            foreach ($pages as $pageKey) {
                $this->generatePageContent($pageKey, $locale, $position, $force);
            }
        }

        $this->info('SEO content generation complete!');
        return 0;
    }

    protected function getTargetLocales(): array
    {
        if ($this->option('priority')) {
            return config('locales.priority', ['en']);
        }

        return [$this->option('locale')];
    }

    protected function generatePageContent(string $pageKey, string $locale, string $position, bool $force): void
    {
        $existing = PageSeoContent::where('page_key', $pageKey)
            ->where('locale', $locale)
            ->first();

        if ($existing && !$force) {
            $this->line("  Skipping {$pageKey} ({$locale}) - already exists");
            return;
        }

        $this->line("  Generating content for: {$pageKey} ({$locale})");

        try {
            $langName = config("locales.supported.{$locale}.name", 'English');
            $siteName = config('app.name', 'My Site');

            $prompt = $this->buildPrompt($pageKey, $langName, $siteName);
            $response = $this->callAnthropic($prompt);
            $content = $this->parseResponse($response);

            if (empty($content['content'])) {
                $this->error("  Failed to generate content for {$pageKey}");
                return;
            }

            PageSeoContent::updateOrCreate(
                ['page_key' => $pageKey, 'locale' => $locale],
                [
                    'title' => $content['title'],
                    'content' => $content['content'],
                    'keywords' => $content['keywords'],
                    'position' => $position,
                    'is_active' => true,
                ]
            );

            $this->info("  Generated content for {$pageKey}");
        } catch (\Exception $e) {
            $this->error("  Error: " . $e->getMessage());
        }
    }

    protected function buildPrompt(string $pageKey, string $langName, string $siteName): string
    {
        return <<<PROMPT
You are an SEO content writer for a website called "{$siteName}".

Generate SEO content for the page: {$pageKey}

Generate content in {$langName}.

Respond in this exact JSON format:
{
    "title": "Section title/heading (20-50 chars)",
    "content": "3-4 paragraphs of unique SEO content (400-600 words). Include relevant keywords naturally.",
    "keywords": "comma,separated,target,keywords"
}

Important rules:
- Content must be unique and valuable
- NO keyword stuffing
- Write for humans first, search engines second
- All text in {$langName}
PROMPT;
    }

    protected function callAnthropic(string $prompt): string
    {
        $apiKey = config('services.anthropic.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('ANTHROPIC_API_KEY not set in config/services.php');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 2048,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Anthropic API error: ' . $response->body());
        }

        return $response->json('content.0.text', '');
    }

    protected function parseResponse(string $response): array
    {
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $response);

        if (preg_match('/\{[\s\S]*\}/u', $cleaned, $matches)) {
            $cleaned = $matches[0];
        }

        $data = json_decode($cleaned, true);

        return [
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'keywords' => $data['keywords'] ?? null,
        ];
    }

    protected function translateExistingContent(string $targetLocale, bool $force): void
    {
        $this->info("  Translating content to {$targetLocale}...");

        $englishContent = PageSeoContent::where('locale', 'en')
            ->where('is_active', true)
            ->get();

        foreach ($englishContent as $content) {
            $existing = PageSeoContent::where('page_key', $content->page_key)
                ->where('locale', $targetLocale)
                ->first();

            if ($existing && !$force) {
                continue;
            }

            try {
                $langName = config("locales.supported.{$targetLocale}.name", $targetLocale);
                $prompt = "Translate the following SEO content to {$langName}. Return ONLY valid JSON with \"title\" and \"content\" keys.\n\nTitle: {$content->title}\n\nContent: {$content->content}";

                $response = $this->callAnthropic($prompt);
                $translated = $this->parseResponse($response);

                PageSeoContent::updateOrCreate(
                    ['page_key' => $content->page_key, 'locale' => $targetLocale],
                    [
                        'title' => $translated['title'] ?? $content->title,
                        'content' => $translated['content'] ?? $content->content,
                        'keywords' => $content->keywords,
                        'position' => $content->position,
                        'is_active' => true,
                    ]
                );
            } catch (\Exception $e) {
                // Continue on error
            }

            usleep(300000);
        }
    }
}
