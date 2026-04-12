<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class SeoService
{
    public function getHomepageSchema(string $siteName, ?string $searchTemplate = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => config('app.url'),
            'inLanguage' => App::getLocale(),
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

    public function getOrganizationSchema(?array $override = null): array
    {
        $org = array_merge(config('seo.organization', []), $override ?? []);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $org['name'] ?? '',
            'url' => $org['url'] ?? '',
        ];

        if (!empty($org['logo'])) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                'url' => $org['logo'],
            ];
        }

        if (!empty($org['email'])) {
            $schema['email'] = $org['email'];
        }

        if (!empty($org['phone'])) {
            $schema['telephone'] = $org['phone'];
            $schema['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => $org['phone'],
                'contactType' => 'customer service',
            ];
        }

        if (!empty($org['same_as'])) {
            $schema['sameAs'] = array_values($org['same_as']);
        }

        return $schema;
    }

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

    public function getItemListSchema(string $name, int $totalItems, array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'numberOfItems' => $totalItems,
            'itemListElement' => array_map(fn(array $item, int $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'url' => $item['url'],
                'name' => $item['name'],
            ], $items, array_keys($items)),
        ];
    }

    public function getBreadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn(array $item, int $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ], $items, array_keys($items)),
        ];
    }

    public function getFaqPageSchema(array $qaPairs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn(array $pair) => [
                '@type' => 'Question',
                'name' => $pair['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $pair['answer'],
                ],
            ], $qaPairs),
        ];
    }

    public function getProfilePageSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'name' => $data['name'] ?? '',
            'url' => $data['url'] ?? '',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $data['name'] ?? '',
                'url' => $data['url'] ?? '',
                'image' => $data['image'] ?? '',
                'description' => $data['description'] ?? '',
            ],
        ];
    }

    public function getArticleSchema(array $data, string $type = 'Article'): array
    {
        $publisher = $this->getOrganizationSchema();
        unset($publisher['@context']);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'headline' => $data['headline'] ?? '',
            'url' => $data['url'] ?? '',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $data['url'] ?? '',
            ],
            'datePublished' => $data['datePublished'] ?? '',
            'dateModified' => $data['dateModified'] ?? $data['datePublished'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $data['author'] ?? '',
            ],
            'publisher' => $publisher,
            'inLanguage' => App::getLocale(),
        ];

        if (!empty($data['image'])) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $data['image'],
                'width' => $data['imageWidth'] ?? 1200,
                'height' => $data['imageHeight'] ?? 630,
            ];
        }

        if (!empty($data['articleSection'])) {
            $schema['articleSection'] = $data['articleSection'];
        }

        if (!empty($data['tags'])) {
            $schema['keywords'] = implode(', ', $data['tags']);
        }

        if (!empty($data['wordCount'])) {
            $schema['wordCount'] = $data['wordCount'];
        }

        return $schema;
    }

    public function getVideoSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'thumbnailUrl' => $data['thumbnailUrl'] ?? '',
            'uploadDate' => $data['uploadDate'] ?? '',
        ];

        if (!empty($data['duration'])) {
            $schema['duration'] = $data['duration'];
        }

        if (!empty($data['contentUrl'])) {
            $schema['contentUrl'] = $data['contentUrl'];
        }

        if (!empty($data['embedUrl'])) {
            $schema['embedUrl'] = $data['embedUrl'];
        }

        if (!empty($data['viewCount'])) {
            $schema['interactionStatistic'] = [
                '@type' => 'InteractionCounter',
                'interactionType' => ['@type' => 'WatchAction'],
                'userInteractionCount' => $data['viewCount'],
            ];
        }

        return $schema;
    }

    public function getProductSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $data['name'] ?? '',
        ];

        if (!empty($data['image'])) {
            $schema['image'] = $data['image'];
        }

        if (!empty($data['description'])) {
            $schema['description'] = $data['description'];
        }

        if (!empty($data['sku'])) {
            $schema['sku'] = $data['sku'];
        }

        if (!empty($data['brand'])) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $data['brand'],
            ];
        }

        if (isset($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => $data['currency'] ?? 'USD',
                'availability' => $data['availability'] ?? 'https://schema.org/InStock',
            ];
        }

        if (!empty($data['ratingValue'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $data['ratingValue'],
                'reviewCount' => $data['reviewCount'] ?? 0,
            ];
        }

        return $schema;
    }

    public function getHowToSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $data['name'] ?? '',
        ];

        if (!empty($data['description'])) {
            $schema['description'] = $data['description'];
        }

        if (!empty($data['image'])) {
            $schema['image'] = $data['image'];
        }

        if (!empty($data['totalTime'])) {
            $schema['totalTime'] = $data['totalTime'];
        }

        if (!empty($data['steps'])) {
            $schema['step'] = array_map(fn(array $step, int $i) => [
                '@type' => 'HowToStep',
                'position' => $i + 1,
                'name' => $step['name'] ?? '',
                'text' => $step['text'] ?? '',
            ], $data['steps'], array_keys($data['steps']));
        }

        return $schema;
    }

    public function getReviewSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'datePublished' => $data['datePublished'] ?? '',
            'reviewBody' => $data['reviewBody'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $data['author'] ?? '',
            ],
        ];

        if (!empty($data['itemReviewed'])) {
            $schema['itemReviewed'] = $data['itemReviewed'];
        }

        if (!empty($data['ratingValue'])) {
            $schema['reviewRating'] = [
                '@type' => 'Rating',
                'ratingValue' => $data['ratingValue'],
                'bestRating' => $data['bestRating'] ?? 5,
            ];
        }

        return $schema;
    }

    public function getEventSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $data['name'] ?? '',
            'startDate' => $data['startDate'] ?? '',
        ];

        if (!empty($data['endDate'])) {
            $schema['endDate'] = $data['endDate'];
        }

        if (!empty($data['description'])) {
            $schema['description'] = $data['description'];
        }

        if (!empty($data['image'])) {
            $schema['image'] = $data['image'];
        }

        if (!empty($data['location'])) {
            $loc = $data['location'];
            $schema['location'] = !empty($loc['url'])
                ? ['@type' => 'VirtualLocation', 'url' => $loc['url']]
                : ['@type' => 'Place', 'name' => $loc['name'] ?? '', 'address' => $loc['address'] ?? ''];
        }

        if (!empty($data['performer'])) {
            $schema['performer'] = [
                '@type' => 'Person',
                'name' => $data['performer'],
            ];
        }

        if (isset($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => $data['currency'] ?? 'USD',
                'availability' => $data['availability'] ?? 'https://schema.org/InStock',
            ];
        }

        return $schema;
    }

    public function getSoftwareApplicationSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $data['name'] ?? '',
        ];

        if (!empty($data['applicationCategory'])) {
            $schema['applicationCategory'] = $data['applicationCategory'];
        }

        if (!empty($data['operatingSystem'])) {
            $schema['operatingSystem'] = $data['operatingSystem'];
        }

        if (isset($data['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $data['price'],
                'priceCurrency' => $data['currency'] ?? 'USD',
            ];
        }

        if (!empty($data['ratingValue'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $data['ratingValue'],
                'reviewCount' => $data['reviewCount'] ?? 0,
            ];
        }

        return $schema;
    }

    public function getSiteNavigationSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_map(fn(array $item) => [
                '@type' => 'SiteNavigationElement',
                'name' => $item['name'] ?? '',
                'url' => $item['url'] ?? '',
            ], $items),
        ];
    }

    public function getWebPageSchema(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $data['name'] ?? '',
            'url' => $data['url'] ?? '',
            'inLanguage' => App::getLocale(),
        ];

        if (!empty($data['speakable'])) {
            $schema['speakable'] = [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => $data['speakable'],
            ];
        }

        if (!empty($data['breadcrumb'])) {
            $schema['breadcrumb'] = $this->getBreadcrumbSchema($data['breadcrumb']);
        }

        return $schema;
    }

    public function buildHreflangUrls(string $englishUrl, string $pathPattern, array $routeParams = []): array
    {
        $locales = config('app.locales', ['en']);
        $hreflangs = [];

        foreach ($locales as $locale) {
            $path = str_replace('{locale}', $locale, $pathPattern);

            foreach ($routeParams as $key => $value) {
                $path = str_replace('{' . $key . '}', $value, $path);
            }

            $hreflangs[] = [
                'hreflang' => $locale,
                'url' => rtrim(config('app.url'), '/') . $path,
            ];
        }

        $hreflangs[] = [
            'hreflang' => 'x-default',
            'url' => $englishUrl,
        ];

        return $hreflangs;
    }

    public function getOpenGraphTags(array $data): array
    {
        $tags = [
            'og:title' => $data['title'] ?? config('seo.title.default'),
            'og:type' => $data['type'] ?? config('seo.og.type', 'website'),
            'og:url' => $data['url'] ?? url()->current(),
            'og:description' => $data['description'] ?? config('seo.description'),
        ];

        if (!empty($data['image'])) {
            $tags['og:image'] = $data['image'];
            $tags['og:image:width'] = $data['image_width'] ?? config('seo.og.image_width', 1200);
            $tags['og:image:height'] = $data['image_height'] ?? config('seo.og.image_height', 630);
            $tags['og:image:alt'] = $data['image_alt'] ?? $tags['og:title'];
        }

        if (!empty($data['site_name'])) {
            $tags['og:site_name'] = $data['site_name'];
        }

        if (!empty($data['locale'])) {
            $tags['og:locale'] = $data['locale'];
        }

        return $tags;
    }

    public function getTwitterCardTags(array $data): array
    {
        return array_filter([
            'twitter:card' => $data['card'] ?? config('seo.twitter.card', 'summary_large_image'),
            'twitter:title' => $data['title'] ?? config('seo.title.default'),
            'twitter:description' => $data['description'] ?? config('seo.description'),
            'twitter:image' => $data['image'] ?? null,
            'twitter:site' => $data['site'] ?? config('seo.twitter.site'),
            'twitter:creator' => $data['creator'] ?? config('seo.twitter.creator'),
        ]);
    }

    public function analyzeContentSalience(string $content, array $targetEntities): array
    {
        $content = mb_strtolower($content);
        $wordCount = str_word_count($content);

        return array_map(function (string $entity) use ($content, $wordCount) {
            $entityLower = mb_strtolower($entity);
            $count = mb_substr_count($content, $entityLower);
            $density = $wordCount > 0 ? round($count / $wordCount, 4) : 0;

            return [
                'entity' => $entity,
                'count' => $count,
                'density' => $density,
                'present' => $count > 0,
            ];
        }, $targetEntities);
    }
}
