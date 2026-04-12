<?php

use App\Services\SeoManager;

if (!function_exists('seo')) {
    /**
     * Get the SeoManager singleton.
     *
     * seo()                       - returns the SeoManager instance
     * seo()->title('My Page')     - set title, returns manager for chaining
     * seo()->get('title')         - get current title value
     */
    function seo(): SeoManager
    {
        return app(SeoManager::class);
    }
}

if (!function_exists('localized_route')) {
    /**
     * Generate a URL for a named route, automatically adding the locale prefix
     * when the current locale is not the default (English).
     */
    function localized_route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $locale = app()->getLocale();

        if ($locale === 'en' || $locale === config('app.fallback_locale', 'en')) {
            return route($name, $parameters, $absolute);
        }

        $localizedName = $name . '.localized';

        if (\Illuminate\Support\Facades\Route::has($localizedName)) {
            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }
            $parameters['locale'] = $locale;
            return route($localizedName, $parameters, $absolute);
        }

        return route($name, $parameters, $absolute);
    }
}

if (!function_exists('country_flag')) {
    /**
     * Convert a 2-letter country code to a flag emoji.
     */
    function country_flag(string $code): string
    {
        $code = strtoupper(trim($code));
        if (strlen($code) !== 2) {
            return '';
        }

        $flag = '';
        foreach (str_split($code) as $char) {
            $flag .= mb_chr(ord($char) - ord('A') + 0x1F1E6);
        }

        return $flag;
    }
}
