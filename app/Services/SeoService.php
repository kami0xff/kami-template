<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class SeoService
{
    /**
     * Generate Schema.org WebSite JSON-LD for the homepage.
     * Enables Google sitelinks search box when SearchAction is present.
     */
    public function getHomepageSchema(string $siteName, ?string $searchTemplate = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => config('app.url'),
        ];

        if ($searchTemplate) {
            $schema['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $searchTemplate,
                ],
                'query-input' => 'required name=search_term_string',
            ];
        }

        return [$schema];
    }

    /**
     * Generate CollectionPage schema for listing/category pages.
     */
    public function getCollectionPageSchema(string $name, string $url, int $numberOfItems, ?string $description = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $name,
            'url' => $url,
            'numberOfItems' => $numberOfItems,
        ];

        if ($description) {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /**
     * Generate ItemList schema for paginated content grids.
     * Helps Google understand listing structure and can produce rich results.
     */
    public function getItemListSchema(string $name, int $totalItems, array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'numberOfItems' => $totalItems,
            'itemListElement' => array_map(fn($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'url' => $item['url'],
                'name' => $item['name'],
            ], $items, array_keys($items)),
        ];
    }

    /**
     * Generate BreadcrumbList schema.
     * Each item: ['name' => '...', 'url' => '...']
     */
    public function getBreadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn($item, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ], $items, array_keys($items)),
        ];
    }

    /**
     * Generate FAQPage schema from Q&A pairs.
     * Each pair: ['question' => '...', 'answer' => '...']
     */
    public function getFaqPageSchema(array $qaPairs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn($qa) => [
                '@type' => 'Question',
                'name' => $qa['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $qa['answer'],
                ],
            ], $qaPairs),
        ];
    }

    /**
     * Generate ProfilePage schema for a person/entity.
     */
    public function getProfilePageSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'dateCreated' => $data['created_at'] ?? null,
            'dateModified' => $data['updated_at'] ?? null,
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $data['name'],
                'image' => $data['image'] ?? null,
                'description' => $data['description'] ?? null,
                'url' => $data['url'],
                'sameAs' => $data['same_as'] ?? [],
            ],
        ];
    }

    /**
     * Build hreflang URL map for a route across all priority locales.
     * Returns: ['en' => 'https://...', 'es' => 'https://...', 'x-default' => 'https://...']
     */
    public function buildHreflangUrls(string $englishUrl, string $pathPattern, array $routeParams = []): array
    {
        $urls = [
            'en' => $englishUrl,
            'x-default' => $englishUrl,
        ];

        foreach (config('locales.priority', []) as $locale) {
            if ($locale !== 'en') {
                $path = str_replace('{locale}', $locale, $pathPattern);
                foreach ($routeParams as $key => $value) {
                    $path = str_replace("{{$key}}", $value, $path);
                }
                $urls[$locale] = url($path);
            }
        }

        return $urls;
    }

    /**
     * Generate Open Graph tags array.
     */
    public function getOpenGraphTags(array $data): array
    {
        return [
            'og:type' => $data['type'] ?? 'website',
            'og:title' => $data['title'],
            'og:description' => $data['description'] ?? '',
            'og:url' => $data['url'] ?? url()->current(),
            'og:image' => $data['image'] ?? '',
            'og:site_name' => config('app.name'),
            'og:locale' => App::getLocale() . '_' . strtoupper(App::getLocale()),
        ];
    }

    /**
     * Generate Twitter Card tags array.
     */
    public function getTwitterCardTags(array $data): array
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $data['title'],
            'twitter:description' => $data['description'] ?? '',
            'twitter:image' => $data['image'] ?? '',
        ];
    }

    /**
     * Analyze keyword density in content for SEO optimization.
     */
    public function analyzeContentSalience(string $content, array $targetEntities): array
    {
        $analysis = [];
        $wordCount = str_word_count($content);

        foreach ($targetEntities as $entity) {
            $count = substr_count(strtolower($content), strtolower($entity));
            $density = $wordCount > 0 ? ($count / $wordCount) * 100 : 0;

            $analysis[$entity] = [
                'count' => $count,
                'density' => round($density, 2),
                'optimal' => $density >= 0.5 && $density <= 2.5,
            ];
        }

        return $analysis;
    }
}
