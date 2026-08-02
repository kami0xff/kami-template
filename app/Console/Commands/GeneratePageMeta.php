<?php

namespace App\Console\Commands;

use App\Services\Anthropic;
use Illuminate\Console\Command;

/**
 * Drafts title/description variants for a page via the Anthropic API and
 * prints them for human review — AI proposes, you pick and edit. Nothing
 * is written anywhere; the paste-ready snippet goes into the 'pages' map
 * in config/seo.php by hand.
 *
 *   php artisan seo:generate-meta items.show --context="Product detail pages for outdoor gear"
 *   php artisan seo:generate-meta home --variants=5 --locale=es
 */
class GeneratePageMeta extends Command
{
    protected $signature = 'seo:generate-meta
                            {page : Page key from the pages map in config/seo.php}
                            {--variants=3 : How many variants to draft}
                            {--locale=en : Locale to write for}
                            {--context= : What the page is about, audience, target keywords}';

    protected $description = 'Draft title/description variants for a page with AI (review, then paste into config/seo.php)';

    protected const TITLE_LIMIT = 60;
    protected const DESCRIPTION_MIN = 140;
    protected const DESCRIPTION_LIMIT = 160;

    public function handle(Anthropic $ai): int
    {
        $page = $this->argument('page');
        $locale = $this->option('locale');
        $count = max(1, (int) $this->option('variants'));

        $current = (config('seo.pages', []))[$page] ?? null;
        if ($current) {
            $this->components->twoColumnDetail('Current title', $current['title'] ?? '—');
            $this->components->twoColumnDetail('Current description', $current['description'] ?? '—');
            $this->newLine();
        }

        try {
            $variants = $this->normalize($ai->completeJson(
                $this->buildPrompt($page, $locale, $count)
            ));
        } catch (\RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $variants = array_values(array_filter(
            $variants,
            fn($v) => !empty($v['title']) && !empty($v['description'])
        ));

        if (empty($variants)) {
            $this->components->error('The AI returned no usable variants — try again or pass more --context.');

            return self::FAILURE;
        }

        $this->table(
            ['#', 'Title', 'Chars', 'Description', 'Chars'],
            array_map(fn($v, $i) => [
                $i + 1,
                $v['title'],
                $this->length($v['title'], self::TITLE_LIMIT),
                $v['description'],
                $this->length($v['description'], self::DESCRIPTION_LIMIT, self::DESCRIPTION_MIN),
            ], $variants, array_keys($variants)),
        );

        $this->components->info('Paste the winner into config/seo.php → \'pages\' (edit first — you are the editor):');
        $this->newLine();
        $this->line($this->snippet($page, $variants[0]));

        return self::SUCCESS;
    }

    protected function buildPrompt(string $page, string $locale, int $count): string
    {
        $langName = config("locales.supported.{$locale}.name", 'English');
        $siteName = config('app.name', 'the site');
        $context = $this->option('context') ?: '(no extra context given — infer from the page key)';
        $titleLimit = self::TITLE_LIMIT;
        $descMin = self::DESCRIPTION_MIN;
        $descLimit = self::DESCRIPTION_LIMIT;
        $current = (config('seo.pages', []))[$page] ?? null;
        $currentNote = $current
            ? "Current values to improve on: title \"{$current['title']}\", description \"{$current['description']}\"."
            : '';

        return <<<PROMPT
You are an SEO copywriter for "{$siteName}".

Draft {$count} distinct title + meta description variants for the page "{$page}".
Context: {$context}
{$currentNote}

Hard constraints:
- Title: max {$titleLimit} characters, primary keyword early, no clickbait, no ALL CAPS.
- Description: {$descMin}–{$descLimit} characters, active voice, a reason to click.
- Language: {$langName}. Write natively, do not translate literally.
- Variants must explore different angles (benefit-led, feature-led, question, brand-led).
- ":tokens" (e.g. :name) may be kept verbatim when the page is dynamic.

Respond with ONLY valid JSON:
{"variants": [{"title": "...", "description": "..."}]}
PROMPT;
    }

    /** Accept {"variants": [...]} or a bare [...] list. */
    protected function normalize(array $data): array
    {
        if (isset($data['variants']) && is_array($data['variants'])) {
            return $data['variants'];
        }

        return array_is_list($data) ? $data : [];
    }

    protected function length(string $value, int $max, ?int $min = null): string
    {
        $len = mb_strlen($value);
        $over = $len > $max || ($min !== null && $len < $min);

        return $len . ($over ? ' (!)' : '');
    }

    protected function snippet(string $page, array $variant): string
    {
        $title = str_replace("'", "\\'", $variant['title']);
        $description = str_replace("'", "\\'", $variant['description']);

        return <<<PHP
        '{$page}' => [
            'title' => '{$title}',
            'description' => '{$description}',
        ],
        PHP;
    }
}
