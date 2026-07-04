<?php

namespace App\Providers;

use App\Services\SeoManager;
use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Scoped, and intentionally never resolved during provider boot: the
        // manager snapshots config('seo') in its constructor, so it must only
        // be instantiated after request middleware (e.g. SetSite) has applied
        // any per-site config overrides.
        $this->app->scoped(SeoManager::class);
    }
}
