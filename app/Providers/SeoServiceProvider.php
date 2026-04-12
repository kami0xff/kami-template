<?php

namespace App\Providers;

use App\Services\SeoManager;
use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(SeoManager::class);
    }

    public function boot(): void
    {
        $preconnects = config("seo.preconnect", []);
        $manager = app(SeoManager::class);

        foreach ($preconnects as $domain) {
            if ($domain) {
                $manager->preconnect($domain);
            }
        }
    }
}
