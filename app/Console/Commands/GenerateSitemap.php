<?php

namespace App\Console\Commands;

use App\Services\Sitemap;
use Illuminate\Console\Command;

/**
 * Writes public/sitemap.xml for the main app from the sources registered on
 * the Sitemap service (AppServiceProvider). Run on deploy and on a schedule
 * when content changes often:
 *
 *   php artisan seo:sitemap
 *
 * A static file beats a dynamic route here: nginx/Caddy serves it without
 * touching PHP, and crawlers fetch sitemaps often.
 */
class GenerateSitemap extends Command
{
    protected $signature = 'seo:sitemap
                            {--output= : Write to this path instead of public/sitemap.xml (skips robots.txt)}';

    protected $description = 'Write public/sitemap.xml from the registered sitemap sources';

    public function handle(Sitemap $sitemap): int
    {
        $urls = $sitemap->urls();
        $path = $this->option('output') ?: public_path('sitemap.xml');

        file_put_contents($path, view('sitemap', ['urls' => $urls])->render());

        $this->components->info(sprintf('Sitemap written to %s (%d URLs).', $path, count($urls)));

        if (!$this->option('output')) {
            $this->updateRobotsTxt();
        }

        return self::SUCCESS;
    }

    /**
     * Keep one "Sitemap:" line in public/robots.txt pointing at the main
     * sitemap. Only a root /sitemap.xml line is touched — additional lines
     * (e.g. a future sitemap index or per-section sitemaps) are left alone.
     */
    protected function updateRobotsTxt(): void
    {
        $path = public_path('robots.txt');
        $line = 'Sitemap: ' . url('/sitemap.xml');
        $contents = is_file($path) ? file_get_contents($path) : "User-agent: *\nAllow: /\n";

        if (preg_match('/^Sitemap: .*\/sitemap\.xml\s*$/m', $contents)) {
            $contents = preg_replace('/^Sitemap: .*\/sitemap\.xml\s*$/m', $line, $contents);
        } else {
            $contents = rtrim($contents) . "\n\n" . $line . "\n";
        }

        file_put_contents($path, $contents);
    }
}
