<?php

namespace App\Http\Controllers\Sites\Concerns;

use App\Services\Sites\Site;
use Illuminate\Support\Facades\View;

/**
 * hreflang plumbing for multi-locale sites: emits the <link rel="alternate">
 * head tags and shares the locale => URL map that the layout's language
 * switcher renders. No-op unless the document exists in at least two locales.
 */
trait SharesAlternates
{
    /**
     * @param  string[]  $locales  Locales in which this document exists.
     */
    protected function shareAlternates(Site $site, string $path, array $locales): void
    {
        if (count($locales) < 2) {
            return;
        }

        $urls = [];

        foreach ($locales as $locale) {
            $urls[$locale] = $site->localizedUrl($path, $locale);

            seo()->rawTag(
                "hreflang:{$locale}",
                '<link rel="alternate" hreflang="' . e($locale) . '" href="' . e($urls[$locale]) . '">',
            );
        }

        // x-default: the default locale is the fallback for unmatched languages.
        seo()->rawTag(
            'hreflang:x-default',
            '<link rel="alternate" hreflang="x-default" href="' . e($site->localizedUrl($path, $site->locale)) . '">',
        );

        View::share('alternateUrls', $urls);
    }
}
