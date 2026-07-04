<?php

namespace App\Console\Commands;

use App\Services\Sites\ContentRepository;
use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
                            {--clean : Remove the site\'s existing static output before building}';

    protected $description = 'Render all static sites to public/static/{domain} for direct Caddy serving';

    public function handle(SiteRegistry $registry, ContentRepository $content): int
    {
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
    }

    /** @return string[] */
    protected function collectPaths(Site $site, ContentRepository $content): array
    {
        $paths = ['/', '/sitemap.xml'];

        foreach ($content->pages($site) as $page) {
            if ($page['path'] !== 'home') {
                $paths[] = '/' . $page['path'];
            }
        }

        // Blade-only pages (site::pages.*) that have no markdown counterpart.
        $viewsDir = $site->viewsPath() . '/pages';
        if (is_dir($viewsDir)) {
            foreach (File::allFiles($viewsDir) as $view) {
                $path = str_replace('.blade.php', '', $view->getRelativePathname());
                if ($path !== 'home') {
                    $paths[] = '/' . $path;
                }
            }
        }

        $posts = $content->posts($site);

        if ($posts->isNotEmpty()) {
            $paths[] = '/blog';
            foreach ($posts as $post) {
                $paths[] = '/blog/' . $post->slug;
            }
        }

        return array_values(array_unique($paths));
    }

    protected function renderPath(string $domain, string $path): \Symfony\Component\HttpFoundation\Response
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
