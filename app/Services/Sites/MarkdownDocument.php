<?php

namespace App\Services\Sites;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

/**
 * A parsed markdown file: YAML front matter + rendered HTML.
 *
 * Front matter reference (all optional):
 *   title:       Page/post title (falls back to the first heading or slug)
 *   description: Meta description (falls back to an excerpt of the content)
 *   date:        Publish date (Y-m-d) — used for sorting and article schema
 *   updated:     Last significant edit (Y-m-d) — dateModified in schema
 *   author:      Author name for article schema / og:article
 *   section:    Article section/category (article:section + articleSection)
 *   tags:        List of tags/keywords
 *   image:       Absolute URL or /path for og:image and article schema
 *   draft:       true to hide everywhere except the local environment
 *
 * Rich article fields (all optional, rendered by the blog templates):
 *   tldr:        List of takeaway bullets, rendered as a TL;DR box
 *   faq:         List of {question, answer} — rendered section + FAQPage schema
 *   quiz:        {question, options: [], answer: <index>, explanation}
 *   related:     List of post slugs to feature as related articles
 *   sources:     List of {title, url} — rendered as a References section
 */
class MarkdownDocument
{
    protected ?string $plainText = null;

    public function __construct(
        public readonly string $slug,
        public readonly array $matter,
        public readonly string $html,
        public readonly int $mtime,
    ) {
    }

    public function title(): string
    {
        if (!empty($this->matter['title'])) {
            return (string) $this->matter['title'];
        }

        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $this->html, $m)) {
            return trim(strip_tags($m[1]));
        }

        return Str::headline($this->slug);
    }

    public function description(): string
    {
        if (!empty($this->matter['description'])) {
            return (string) $this->matter['description'];
        }

        return Str::limit($this->plainText(), 158, '');
    }

    public function date(): ?CarbonImmutable
    {
        return isset($this->matter['date'])
            ? CarbonImmutable::parse($this->matter['date'])
            : null;
    }

    public function updated(): ?CarbonImmutable
    {
        return isset($this->matter['updated'])
            ? CarbonImmutable::parse($this->matter['updated'])
            : $this->date();
    }

    public function author(): ?string
    {
        return $this->matter['author'] ?? null;
    }

    public function section(): ?string
    {
        return $this->matter['section'] ?? null;
    }

    /** @return string[] */
    public function tags(): array
    {
        $tags = $this->matter['tags'] ?? [];

        return is_array($tags) ? array_map('strval', $tags) : [(string) $tags];
    }

    public function image(): ?string
    {
        return $this->matter['image'] ?? null;
    }

    public function isDraft(): bool
    {
        return (bool) ($this->matter['draft'] ?? false);
    }

    /** @return string[] */
    public function tldr(): array
    {
        return array_map('strval', (array) ($this->matter['tldr'] ?? []));
    }

    /** @return array<int, array{question: string, answer: string}> */
    public function faq(): array
    {
        return array_values(array_filter(
            (array) ($this->matter['faq'] ?? []),
            fn($item) => is_array($item) && !empty($item['question']) && !empty($item['answer'])
        ));
    }

    /** @return array{question: string, options: string[], answer: int, explanation?: string}|null */
    public function quiz(): ?array
    {
        $quiz = $this->matter['quiz'] ?? null;

        if (!is_array($quiz) || empty($quiz['question']) || count($quiz['options'] ?? []) < 2) {
            return null;
        }

        $quiz['answer'] = (int) ($quiz['answer'] ?? 0);

        return $quiz;
    }

    /** @return string[] */
    public function related(): array
    {
        return array_map('strval', (array) ($this->matter['related'] ?? []));
    }

    /** @return array<int, array{title: string, url: string}> */
    public function sources(): array
    {
        return array_values(array_filter(
            (array) ($this->matter['sources'] ?? []),
            fn($item) => is_array($item) && !empty($item['url'])
        ));
    }

    public function excerpt(int $limit = 200): string
    {
        return Str::limit($this->plainText(), $limit);
    }

    public function wordCount(): int
    {
        return str_word_count($this->plainText());
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil($this->wordCount() / 220));
    }

    protected function plainText(): string
    {
        return $this->plainText ??= trim(preg_replace('/\s+/', ' ', strip_tags($this->html)) ?? '');
    }
}
