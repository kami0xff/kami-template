<?php

namespace App\Services\Sites;

/**
 * Discovers sites from `resources/sites/{key}/site.php` and resolves them by
 * key or domain. Discovery happens once per process; adding a site requires
 * a fresh request in dev (automatic) or a redeploy in production.
 */
class SiteRegistry
{
    /** @var array<string, Site>|null */
    protected ?array $sites = null;

    public function basePath(): string
    {
        return resource_path('sites');
    }

    /** @return array<string, Site> */
    public function all(): array
    {
        return $this->sites ??= $this->discover();
    }

    public function get(string $key): ?Site
    {
        return $this->all()[$key] ?? null;
    }

    public function byDomain(string $host): ?Site
    {
        foreach ($this->all() as $site) {
            if (in_array($host, $site->domains, true)) {
                return $site;
            }
        }

        return null;
    }

    /** @return array<string, Site> */
    protected function discover(): array
    {
        $sites = [];

        foreach (glob($this->basePath() . '/*/site.php') ?: [] as $file) {
            $path = dirname($file);
            $key = basename($path);
            $sites[$key] = Site::fromConfig($key, $path, require $file);
        }

        return $sites;
    }
}
