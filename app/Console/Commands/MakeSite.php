<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Scaffolds a new static site under resources/sites/{key}:
 *
 *   php artisan site:make myblog myblog.com
 *   php artisan site:make myblog myblog.com --name="My Blog" --with-www
 */
class MakeSite extends Command
{
    protected $signature = 'site:make
                            {key : Site key (lowercase, used as the folder name)}
                            {domain : Canonical domain (e.g. myblog.com)}
                            {--name= : Display name (defaults to the key in headline case)}
                            {--with-www : Also register www.{domain} (301-redirects to the canonical domain)}';

    protected $description = 'Scaffold a new static site (site.php, starter home page and blog post)';

    public function handle(): int
    {
        $key = Str::slug($this->argument('key'));
        $domain = strtolower($this->argument('domain'));
        $name = $this->option('name') ?: Str::headline($key);
        $path = resource_path("sites/{$key}");

        if (File::exists($path)) {
            $this->components->error("Site [{$key}] already exists at {$path}");

            return self::FAILURE;
        }

        $domains = $this->option('with-www')
            ? "['{$domain}', 'www.{$domain}']"
            : "['{$domain}']";

        File::ensureDirectoryExists("{$path}/content/blog");
        File::ensureDirectoryExists("{$path}/content/pages");
        File::ensureDirectoryExists("{$path}/views/pages");

        File::put("{$path}/site.php", <<<PHP
        <?php

        return [
            // Display name — used as the title suffix, og:site_name, and in schema.
            'name' => '{$name}',

            // First domain is canonical; the rest 301-redirect to it.
            'domains' => {$domains},

            'locale' => 'en',

            // Optional: override any key from config/seo.php for this site.
            'seo' => [
                'description' => 'Welcome to {$name}.',
                // 'theme_color' => '#2563eb',
                // 'twitter' => ['site' => '@handle'],
                // 'organization' => ['logo' => 'https://{$domain}/logo.png'],
            ],
        ];

        PHP);

        File::put("{$path}/content/pages/home.md", <<<MD
        ---
        title: {$name}
        description: Welcome to {$name}.
        ---

        # Welcome to {$name}

        This page is `content/pages/home.md`. Replace it, or create
        `views/pages/home.blade.php` for a fully custom home page
        (Blade views win over markdown files).

        - Blog posts live in `content/blog/*.md` — the filename is the URL slug.
        - Static pages live in `content/pages/*.md` (nesting allowed).
        - Run `php artisan site:build {$key}` to generate the static snapshot.

        MD);

        File::put("{$path}/content/blog/hello-world.md", <<<MD
        ---
        title: Hello World
        description: The first post on {$name}.
        date: {$this->currentDate()}
        author: {$name}
        tags: [news]
        ---

        # Hello World

        This is your first post. Front matter drives all the SEO:
        title, meta description, publish/update dates, author, section,
        tags, and og:image — see the README for the full reference.

        MD);

        $this->components->info("Site [{$key}] created at resources/sites/{$key}");

        $steps = [];

        if (!config('sites.enabled')) {
            $steps[] = 'Enable static sites: set SITES_ENABLED=true in .env (and .env.production)';
        }

        $steps[] = "Point the domain: add {$domain} to your Cloudflare tunnel / DNS";
        $steps[] = "Preview locally: curl -H 'Host: {$domain}' http://localhost:8787/";
        $steps[] = "Build the static snapshot: php artisan site:build {$key}";

        $this->components->bulletList($steps);

        return self::SUCCESS;
    }

    protected function currentDate(): string
    {
        return now()->toDateString();
    }
}
