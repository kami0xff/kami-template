<?php

namespace App\Console\Commands;

use App\Services\Sites\ContentRepository;
use App\Services\Sites\ImageProcessor;
use App\Services\Sites\Site;
use App\Services\Sites\SitemapGenerator;
use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders every page of every registered site to static HTML under
 * public/static/{domain}/..., where Caddy serves it directly — PHP never
 * runs for a cached page in production.
 *
 * Run on deploy (deploy.sh) and whenever content changes:
 *   php artisan site:build            build all sites
 *   php artisan site:build mysite     build one site
 *   php artisan site:build --clean    delete each site's output dir first
 */
class BuildStaticSites extends Command
{
    protected $signature = 'site:build
                            {site? : Only build this site key}
                            {--clean : Remove the site\'s existing static output before building}
                            {--pages : Also emit Cloudflare Pages artifacts (_worker.js lead forwarder + _routes.json)}';

    protected $description = 'Render all static sites to public/static/{domain} for direct Caddy serving';

    public function handle(SiteRegistry $registry, ContentRepository $content): int
    {
        if (!config('sites.enabled')) {
            $this->components->info('Static sites are disabled (set SITES_ENABLED=true to enable) — nothing to build.');

            return self::SUCCESS;
        }

        $sites = $this->argument('site')
            ? array_filter([$registry->get($this->argument('site'))])
            : $registry->all();

        if (empty($sites)) {
            $this->components->info(
                $this->argument('site')
                    ? "Site [{$this->argument('site')}] not found under resources/sites/."
                    : 'No sites registered under resources/sites/ — nothing to build.'
            );

            return $this->argument('site') ? self::FAILURE : self::SUCCESS;
        }

        foreach ($sites as $site) {
            $this->buildSite($site, $content);
        }

        return self::SUCCESS;
    }

    protected function buildSite(Site $site, ContentRepository $content): void
    {
        $domain = $site->canonicalDomain();
        $outputDir = public_path('static/' . $domain);

        $this->components->info("Building [{$site->key}] -> {$domain}");

        if ($this->option('clean')) {
            File::deleteDirectory($outputDir);
        }

        // Images first: originals copied + WebP variants generated (only
        // new/changed sources are processed on subsequent builds).
        if ($count = app(ImageProcessor::class)->buildAll($site)) {
            $this->components->twoColumnDetail('/images (pipeline)', "{$count} file(s) written");
        }

        $paths = $this->collectPaths($site, $content);

        foreach ($paths as $path) {
            $response = $this->renderPath($domain, $path);

            if ($response->getStatusCode() !== 200) {
                $this->components->twoColumnDetail($path, "<error>HTTP {$response->getStatusCode()} — skipped</error>");

                continue;
            }

            $file = $this->outputFile($outputDir, $path);
            File::ensureDirectoryExists(dirname($file));
            File::put($file, $response->getContent());

            $this->components->twoColumnDetail($path, str_replace(public_path(), 'public', $file));
        }

        $this->writeNotFoundPage($domain, $outputDir);

        if ($this->option('pages')) {
            $this->writePagesArtifacts($site, $outputDir);
        }

        if ($site->search) {
            $this->buildSearchIndex($outputDir);
        }
    }

    /**
     * Branded 404 as a static file. Cloudflare Pages serves a root 404.html
     * automatically for unknown paths; on the Caddy hub PHP renders the same
     * view dynamically, so the file is simply unused there.
     */
    protected function writeNotFoundPage(string $domain, string $outputDir): void
    {
        $response = $this->renderPath($domain, '/__static-404-placeholder__');

        if ($response->getStatusCode() !== 404) {
            return;
        }

        File::put($outputDir . '/404.html', $response->getContent());
        $this->components->twoColumnDetail('/404.html (branded)', str_replace(public_path(), 'public', $outputDir . '/404.html'));
    }

    /**
     * Cloudflare Pages needs a serverless stand-in for the one dynamic
     * endpoint the sites have: POST /lead. The worker mirrors LeadController
     * (honeypot, validation, HMAC-signed relay to the admin API) and
     * _routes.json restricts worker invocation to /lead, so every other
     * request is served as a pure static asset.
     *
     * Only emitted with --pages: on the Caddy hub these files would be
     * served as plain (harmless but pointless) static files.
     */
    protected function writePagesArtifacts(Site $site, string $outputDir): void
    {
        $worker = str_replace(
            ['__SITE_KEY__', '__SITE_DOMAIN__'],
            [$site->key, $site->canonicalDomain()],
            File::get(base_path('stubs/pages-worker.js')),
        );

        File::put($outputDir . '/_worker.js', $worker);
        File::put($outputDir . '/_routes.json', (string) json_encode([
            'version' => 1,
            'include' => ['/lead'],
            'exclude' => [],
        ]));

        $this->components->twoColumnDetail('/_worker.js + /_routes.json (Pages)', 'lead forwarder emitted');
    }

    /**
     * Index the freshly built HTML with Pagefind (writes the search bundle
     * to {output}/pagefind/, served statically by Caddy). Dev uses the npm
     * binary; the production image ships /usr/local/bin/pagefind.
     */
    protected function buildSearchIndex(string $outputDir): void
    {
        $binary = collect([base_path('node_modules/.bin/pagefind'), '/usr/local/bin/pagefind'])
            ->first(fn(string $bin) => is_executable($bin));

        if ($binary === null) {
            $this->components->twoColumnDetail('/pagefind (search)', '<comment>skipped — pagefind not installed (npm install)</comment>');

            return;
        }

        try {
            $result = Process::timeout(300)->run([$binary, '--site', $outputDir]);

            $this->components->twoColumnDetail(
                '/pagefind (search)',
                $result->successful() ? 'index built' : '<error>failed: ' . trim($result->errorOutput()) . '</error>'
            );
        } catch (\Throwable $e) {
            $this->components->twoColumnDetail('/pagefind (search)', "<error>failed: {$e->getMessage()}</error>");
        }
    }

    /** @return string[] */
    protected function collectPaths(Site $site, ContentRepository $content): array
    {
        // The sitemap generator is the canonical list of content URLs.
        $paths = collect(app(SitemapGenerator::class)->urls($site))
            ->map(fn(array $url) => '/' . ltrim(str_replace($site->url('/'), '', $url['loc']), '/'))
            ->all();

        $paths[] = '/sitemap.xml';
        $paths[] = '/feed.xml';
        $paths[] = '/robots.txt';

        // Every extra locale has its own RSS feed.
        foreach ($site->extraLocales() as $locale) {
            $paths[] = "/{$locale}/feed.xml";
        }

        if ($site->search) {
            $paths[] = '/search';
        }

        // Blade-only pages (site::pages.*) that have no markdown counterpart.
        // Blade views serve every locale (same view, localized data).
        $viewsDir = $site->viewsPath() . '/pages';
        if (is_dir($viewsDir)) {
            foreach (File::allFiles($viewsDir) as $view) {
                $path = str_replace('.blade.php', '', $view->getRelativePathname());

                if ($path !== 'home') {
                    $paths[] = '/' . $path;
                }

                foreach ($site->extraLocales() as $locale) {
                    $paths[] = '/' . $locale . ($path === 'home' ? '' : '/' . $path);
                }
            }
        }

        return array_values(array_unique($paths));
    }

    protected function renderPath(string $domain, string $path): Response
    {
        // Scoped services (like the SeoManager) live for one "request" —
        // reset them between renders so pages don't inherit each other's state.
        app()->forgetScopedInstances();

        $request = Request::create("https://{$domain}{$path}");

        $kernel = app(HttpKernel::class);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        return $response;
    }

    protected function outputFile(string $outputDir, string $path): string
    {
        // Files with an extension (sitemap.xml) are written as-is; HTML pages
        // become {path}/index.html so Caddy can serve clean URLs.
        if (str_contains(basename($path), '.')) {
            return $outputDir . $path;
        }

        return rtrim($outputDir . $path, '/') . '/index.html';
    }
}
