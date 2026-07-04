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

## Static Sites (multi-domain)

One project can host many static sites — each site is a folder under
`resources/sites/{key}/`, routed by its domain and served as pre-built HTML
in production (Caddy serves the files directly; PHP never runs for a cached page).

### Create a site

```bash
php artisan site:make myblog myblog.com --name="My Blog" --with-www
# or: make site-new KEY=myblog DOMAIN=myblog.com
```

This scaffolds:

```
resources/sites/myblog/
  site.php               — name, domains, locale, per-site SEO overrides
  content/
    blog/hello-world.md  — blog post (filename = URL slug -> /blog/hello-world)
    pages/home.md        — static page (path = URL -> /)
  views/pages/           — optional Blade pages (win over markdown)
```

Then point the domain at the server (Cloudflare tunnel/DNS). The first domain
in `site.php` is canonical; others (e.g. www.) 301-redirect to it.

### URL structure per site

| URL | Source |
|-----|--------|
| `/` | `views/pages/home.blade.php` or `content/pages/home.md` |
| `/blog` | index of `content/blog/*.md`, newest first |
| `/blog/{slug}` | `content/blog/{slug}.md` |
| `/{path}` | `views/pages/{path}.blade.php` or `content/pages/{path}.md` (nesting allowed) |
| `/sitemap.xml` | generated (includes lastmod from front matter) |

### Content = markdown + front matter

```markdown
---
title: My Post Title
description: Meta description for search results.
date: 2026-07-01          # article:published_time + datePublished
updated: 2026-07-10       # article:modified_time + dateModified
author: Jane Doe
section: Guides
tags: [laravel, seo]
image: https://myblog.com/img/cover.png   # og:image + schema image
draft: true               # hidden everywhere except local dev
---

# My Post Title

GitHub-flavored markdown body...
```

Every field feeds the SEO layer automatically: meta title/description,
canonical URL, Open Graph article tags, BlogPosting + BreadcrumbList JSON-LD,
and the sitemap. Only `title` is really needed — everything else has fallbacks.

### Publishing workflow

1. Edit or add a `.md` file (drafts preview in local dev)
2. Commit and push to `main`
3. `deploy.sh` runs `php artisan site:build --clean`, which renders every page
   to `public/static/{domain}/.../index.html`

Caddy serves those files directly with a 5-minute cache header and falls back
to dynamic rendering on a miss. To publish content without a code deploy, run
`make prod-shell` then `php artisan site:build` after pulling.

### Per-site SEO / branding

`site.php` can override any key from `config/seo.php` for that site only
(description, theme color, twitter handles, organization schema, …). The site
name automatically becomes the title suffix, og:site_name, and Organization
name. The shared look lives in `resources/views/sites/layout.blade.php`; a
site can override it (or any shared view) by creating the same file under
`resources/sites/{key}/views/`.

The `example` site under `resources/sites/example/` is a living reference and
is used by the test suite (domain `example.test`).

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
