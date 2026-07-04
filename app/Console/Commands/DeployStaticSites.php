<?php

namespace App\Console\Commands;

use App\Services\Sites\Site;
use App\Services\Sites\SiteRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

/**
 * Deploys static sites to Cloudflare Pages (Direct Upload via wrangler):
 *
 *   php artisan site:deploy            build + deploy every site
 *   php artisan site:deploy mysite     build + deploy one site
 *   php artisan site:deploy mysite --no-build
 *
 * Each site becomes one Pages project (named after the site key). The build
 * runs with --pages, so the output carries a _worker.js that handles POST
 * /lead serverlessly — no hub server involved at runtime.
 *
 * Auth: `npx wrangler login` once, or set CLOUDFLARE_API_TOKEN +
 * CLOUDFLARE_ACCOUNT_ID for non-interactive use (deploy scripts, CI).
 *
 * After the first deploy of a site (once per site, in the dashboard):
 *   1. Pages project -> Custom domains -> add the site's domain(s)
 *   2. Pages project -> Settings -> Variables -> LEADS_WEBHOOK_URL +
 *      LEADS_WEBHOOK_SECRET (the worker rejects leads without them)
 */
class DeployStaticSites extends Command
{
    protected $signature = 'site:deploy
                            {site? : Only deploy this site key}
                            {--project= : Pages project name (defaults to the site key; only with a single site)}
                            {--no-build : Skip the build and deploy the existing static output}';

    protected $description = 'Build and deploy static sites to Cloudflare Pages via wrangler';

    public function handle(SiteRegistry $registry): int
    {
        if (!config('sites.enabled')) {
            $this->components->error('Static sites are disabled (set SITES_ENABLED=true).');

            return self::FAILURE;
        }

        $sites = $this->argument('site')
            ? array_filter([$registry->get($this->argument('site'))])
            : $registry->all();

        if (empty($sites)) {
            $this->components->error(
                $this->argument('site')
                    ? "Site [{$this->argument('site')}] not found under resources/sites/."
                    : 'No sites registered under resources/sites/.'
            );

            return self::FAILURE;
        }

        if ($this->option('project') && count($sites) > 1) {
            $this->components->error('--project can only be used when deploying a single site.');

            return self::FAILURE;
        }

        if (Process::run(['which', 'npx'])->failed()) {
            $this->components->error('npx not found — wrangler needs Node.js (https://nodejs.org).');

            return self::FAILURE;
        }

        foreach ($sites as $site) {
            if (!$this->deploySite($site)) {
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    protected function deploySite(Site $site): bool
    {
        $project = $this->option('project') ?: $site->key;
        $outputDir = public_path('static/' . $site->canonicalDomain());

        if (!$this->option('no-build')) {
            $this->call('site:build', ['site' => $site->key, '--clean' => true, '--pages' => true]);
        }

        if (!is_file($outputDir . '/index.html')) {
            $this->components->error("No static output for [{$site->key}] at {$outputDir} — run without --no-build.");

            return false;
        }

        if (!is_file($outputDir . '/_worker.js')) {
            $this->components->warn("Output was built without --pages: the /lead endpoint will not work on Pages. Rebuild with `site:build {$site->key} --pages`.");
        }

        $this->components->info("Deploying [{$site->key}] -> Pages project [{$project}]");

        // Idempotent: fails harmlessly when the project already exists.
        Process::timeout(120)->run([
            'npx', 'wrangler', 'pages', 'project', 'create', $project,
            '--production-branch', 'main',
        ]);

        $deploy = Process::timeout(900)->run([
            'npx', 'wrangler', 'pages', 'deploy', $outputDir,
            '--project-name', $project,
            '--branch', 'main',
            '--commit-dirty=true',
        ], fn(string $type, string $output) => $this->output->write($output));

        if ($deploy->failed()) {
            $this->components->error("Deploy failed for [{$site->key}] — see wrangler output above.");

            return false;
        }

        $this->components->info("Deployed [{$site->key}].");
        $this->components->bulletList([
            "First deploy only — attach the domain: Pages -> {$project} -> Custom domains -> {$site->canonicalDomain()}",
            "First deploy only — set LEADS_WEBHOOK_URL + LEADS_WEBHOOK_SECRET: Pages -> {$project} -> Settings -> Variables",
        ]);

        return true;
    }
}
