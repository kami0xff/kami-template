<?php

namespace App\Services\Sites;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Reads/writes topic cluster plans: resources/sites/{key}/clusters/{name}.yaml
 *
 * A plan is created by `site:cluster` and is meant to be reviewed and edited
 * by hand (prune spokes, tweak titles) before drafting. Shape:
 *
 *   topic: Backyard pergolas
 *   keyword: pergola
 *   pillar:
 *     slug: pergola-guide
 *     title: "Pergola Guide: Types, Costs, and How to Choose"
 *     keyword: pergola
 *     status: planned | drafted
 *   spokes:
 *     - slug: pergola-cost
 *       title: How Much Does a Pergola Cost in 2026?
 *       keyword: pergola cost
 *       intent: commercial
 *       angle: One-line brief of what this article covers
 *       status: planned | drafted
 */
class ClusterRepository
{
    public function path(Site $site, string $name): string
    {
        return $site->path . "/clusters/{$name}.yaml";
    }

    /** @return array<string, array> cluster name => plan */
    public function all(Site $site): array
    {
        $dir = $site->path . '/clusters';

        if (!File::isDirectory($dir)) {
            return [];
        }

        $clusters = [];

        foreach (File::files($dir) as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'], true)) {
                $clusters[$file->getFilenameWithoutExtension()] = Yaml::parseFile($file->getPathname());
            }
        }

        ksort($clusters);

        return $clusters;
    }

    public function get(Site $site, string $name): ?array
    {
        $file = $this->path($site, $name);

        return File::exists($file) ? Yaml::parseFile($file) : null;
    }

    public function save(Site $site, string $name, array $plan): void
    {
        File::ensureDirectoryExists(dirname($this->path($site, $name)));
        File::put($this->path($site, $name), Yaml::dump($plan, 4, 2));
    }

    public function isPillar(array $plan, string $slug): bool
    {
        return ($plan['pillar']['slug'] ?? null) === $slug;
    }
}
