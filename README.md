# Kami Template

Production-ready Laravel starter with built-in SEO infrastructure and multi-locale support.

## Stack

- PHP 8.3 + Laravel 12
- FrankenPHP (production) / PHP dev server (local)
- PostgreSQL 16, Redis 7
- Docker Compose, GitHub Actions CI/CD

## Quick Start

Everything runs in Docker — you don't need PHP/Composer/Node on the host.

```bash
# 1. Copy the template (clone or cp), then enter it
git clone <this-repo> /var/www/myapp
cd /var/www/myapp

# 2. Replace template placeholders (__APP_NAME__/__APP_SLUG__/__APP_DOMAIN__)
make configure NAME="My App" SLUG=myapp DOMAIN=myapp.com

# 3. Bootstrap everything: .env, containers, composer install,
#    app key, migrations, and a first asset build
make setup
```

App runs at http://localhost:8787. Run `make help` for all commands.
Use `make assets` for a Vite rebuild-on-change watcher during development.

> `SLUG` must be lowercase — it's used for Docker container/volume names and
> the Postgres database/user. (The Composer package name stays `kami/template`
> so the committed `composer.lock` remains valid.)

## Dev Tooling

This template ships with AI/IDE tooling enabled by default (all `require-dev`, never loaded in production):

| Package | Purpose |
|---------|---------|
| `barryvdh/laravel-ide-helper` | PHPDoc stubs for facades/models so Cursor autocompletes correctly |
| `barryvdh/laravel-debugbar` | In-browser debug toolbar (active only when `APP_DEBUG=true`) |
| `laravel/boost` | Laravel-aware MCP server + version-specific AI guidelines for agents |

### IDE Helper

```bash
make ide-helper   # regenerate _ide_helper.php, model docs, and meta
```

Generated files are git-ignored and refreshed automatically on `composer update`.

### Testing (Pest)

Tests live in `tests/` and run with [Pest](https://pestphp.com). Feature tests use an
in-memory SQLite database (see `phpunit.xml`), so they need no running Postgres:

```bash
make test          # inside the dev container
# or directly:
php artisan test
```

### Laravel Boost (MCP)

The Boost MCP server is **pre-wired for Cursor** in `.cursor/mcp.json`, routed through the
dev container via `docker exec` (host PHP is not required):

```json
{
  "mcpServers": {
    "laravel-boost": {
      "command": "docker",
      "args": ["exec", "-i", "__APP_SLUG__-app-dev", "php", "artisan", "boost:mcp"]
    }
  }
}
```

Setup steps after cloning the template:

1. Replace `__APP_SLUG__` everywhere (this is the standard template placeholder) so the
   container name in `.cursor/mcp.json` matches `docker-compose.dev.yml`.
2. Start the stack: `make dev`
3. Generate AI guidelines/skills: `make boost-install` (interactive — pick your agents).
   Skip/deselect any host-`php` MCP entry it offers to write; the Docker-routed
   `.cursor/mcp.json` above already handles the MCP connection.
4. Reload Cursor so it picks up the `laravel-boost` MCP server.

Keep guidelines current as packages change with `make boost-update`. Boost's generated
guideline/config files (`boost.json`, `.mcp.json`, `AGENTS.md`, `CLAUDE.md`) are git-ignored;
`.cursor/mcp.json` is committed intentionally.

## SEO Infrastructure

### Components

| Component | Path | Description |
|-----------|------|-------------|
| `<x-seo.schema>` | `resources/views/components/seo/schema.blade.php` | Outputs JSON-LD `<script>` blocks |
| `<x-seo.hreflang>` | `resources/views/components/seo/hreflang.blade.php` | Outputs `<link rel="alternate" hreflang>` |
| `<x-seo.breadcrumbs>` | `resources/views/components/seo/breadcrumbs.blade.php` | Breadcrumb navigation |
| `<x-seo.content-block>` | `resources/views/components/seo/content-block.blade.php` | AI-generated SEO text blocks |

### Services

| Class | Description |
|-------|-------------|
| `SeoService` | Builds JSON-LD schemas (WebSite, CollectionPage, ItemList, BreadcrumbList, FAQPage, ProfilePage) |
| `PageSeoContent` | Model for database-backed SEO content with locale fallback |

### Artisan Commands

```bash
# Generate SEO content for a page
php artisan seo:generate-page-content --pages=home

# Translate to all priority locales
php artisan seo:generate-page-content --translate --priority

# Force regenerate
php artisan seo:generate-page-content --pages=home --force
```

Requires `ANTHROPIC_API_KEY` in `config/services.php`:

```php
'anthropic' => [
    'api_key' => env('ANTHROPIC_API_KEY'),
],
```

### Locale System

**Routing pattern:** English uses bare URLs (`/about`), other locales use prefix (`/es/about`).

**Middleware:**
- `detect.locale` — Reads browser `Accept-Language`, redirects on first visit
- `set.locale` — Sets `App::setLocale()` from URL prefix

**Helper:** `localized_route('name', $params)` — auto-adds locale prefix.

**Config:** `config/locales.php` — 15 locales pre-configured (add more as needed).

**Translations:** Use `lang/{locale}/*.php` files with `__('file.key')` dot-notation.

### Per-Page SEO Checklist

Every page must define:

```blade
@section('title', 'Page Title - Site Name')           {{-- <60 chars --}}
@section('meta_description', 'Description here...')     {{-- 150-160 chars --}}
@section('canonical', url('/my-page'))                  {{-- True URL --}}

@push('seo-pagination')
<x-seo.hreflang :urls="$hreflangUrls" />               {{-- Locale alternates --}}
<x-seo.schema :schemas="$seoSchemas" />                 {{-- JSON-LD --}}
@endpush
```

### Showcase Page

Visit `/seo-showcase` to see all components in action with usage examples.

## Deployment

```bash
# Deploy to production (Docker)
bash deploy.sh

# Or manually
docker compose -f docker-compose.prod.yml up -d --build
```

See `DEPLOYMENT.md` for full production setup guide.

## File Structure

```
app/
  Console/Commands/GeneratePageSeoContent.php
  Helpers/helpers.php
  Http/Middleware/{DetectLocale,SetLocale}.php
  Models/PageSeoContent.php
  Services/SeoService.php
config/locales.php
database/migrations/create_page_seo_content_table.php
lang/en/common.php
resources/views/
  components/seo/{schema,hreflang,breadcrumbs,content-block}.blade.php
  layouts/app.blade.php
  pages/seo-showcase.blade.php
routes/web.php
```
