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

### Keeping projects in sync with the template (avoiding drift)

Copied projects fork the moment they are created — template fixes don't
reach them by themselves. The workflow that keeps updates flowing:

1. **Copy with `git clone`, never `cp`** — cloning preserves the template's
   history, which is what makes future merges trivial three-way merges
   instead of conflict storms.
2. **Keep the template as a second remote** in every project:

```bash
git clone <template-repo> /var/www/myapp && cd /var/www/myapp
git remote rename origin template
git remote add origin <project-repo>       # the project's own repo
git push -u origin main
```

3. **Pull template improvements periodically:**

```bash
git fetch template
git merge template/main      # then: make check
```

Conflicts stay rare because project-specific changes concentrate in files
the template rarely touches (`resources/sites/`, `.env*`, compose files).
When a project intentionally diverges (e.g. a trimmed `docker-compose.prod.yml`
for a static hub), git remembers your resolution direction after the first merge.

If the family of projects grows past a handful, the mature next step is
extracting the sites engine into a Composer package that projects
`composer require` — updates then arrive with `composer update` instead of
merges. Not worth the overhead below ~4-5 projects.

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

### Code Quality (Pint + Larastan)

```bash
make lint       # code style check (Pint, rules in pint.json)
make lint-fix   # auto-fix style
make stan       # static analysis (Larastan, level 5 — phpstan.neon)
make check      # lint + stan + test — run before pushing
```

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

## Application Architecture (Actions / Queries / DTOs)

Dynamic application code follows a read/write split — the same architecture the
static sites use at the network level (reads from baked output or a read-only
source, writes through the admin API):

| Layer | Folder | Rule |
|-------|--------|------|
| **DTOs** | `app/DTOs/` | Immutable (`final readonly`) data crossing layer boundaries. Controllers validate, build a DTO, pass it on — Actions and Queries never touch the request. |
| **Actions** | `app/Actions/` | Every write. One class per operation, one `handle(SomeData $data)` method. The single place for side effects (events, cache busting, notifications). |
| **Queries** | `app/Queries/` | Every read. The only layer that knows the schema, so a schema change breaks one folder — not thirty controllers. Queries never write. |

Scaffold with the bundled generators (nesting by domain works):

```bash
php artisan make:dto Orders/OrderData
php artisan make:action Orders/CreateOrder
php artisan make:query Orders/OrderIndexQuery
```

Reference implementations for the `User` model ship in each folder
(`UserData`, `CreateUser`, `UserIndexQuery`).

### Reading from a replica or a shared admin database

`config/database.php` defines a `read` connection for Query classes that read
from a Postgres replica or another project's database. Point `DB_READ_*` at a
**SELECT-only database user** (ideally querying views the owning project
exposes as its read contract, not raw tables) and use it explicitly:

```php
User::on('read')->...
DB::connection('read')->...
```

It falls back to the main connection when `DB_READ_*` is unset, so Query
classes written against it work before a replica exists.

## Static Sites (multi-domain)

One project can host many static sites — each site is a folder under
`resources/sites/{key}/`, routed by its domain and served as pre-built HTML
in production (Caddy serves the files directly; PHP never runs for a cached page).

**Opt-in.** The feature is off by default so regular apps carry none of it:

```bash
# .env / .env.production
SITES_ENABLED=true
```

When disabled, no site routes are registered and `site:build` is a no-op. A
project can be sites-only, a regular app, or both — the main app keeps its
routes on the primary domain while sites answer on theirs.

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
| `/feed.xml` | RSS 2.0 feed of published posts (auto-advertised in the page head) |
| `/sitemap.xml` | generated by `SitemapGenerator` (includes lastmod from front matter) |

### Content = markdown + front matter

```markdown
---
title: My Post Title
description: Meta description for search results.
date: 2026-07-01          # article:published_time + datePublished
updated: 2026-07-10       # article:modified_time + dateModified
author: Jane Doe          # omit to use the site author (E-E-A-T box + Person schema)
section: Guides
tags: [laravel, seo]
image: https://myblog.com/img/cover.png   # og:image + schema image
draft: true               # hidden everywhere except local dev
tldr:                     # rendered as a TL;DR takeaway box
  - First takeaway.
faq:                      # rendered section + FAQPage JSON-LD
  - question: Is this indexed?
    answer: Yes — FAQ answers render on the page and as FAQPage schema.
quiz:                     # interactive quick-check (crawlable text, inline JS)
  question: Which field controls the URL?
  options: [The title, The filename]
  answer: 1
  explanation: The filename is the slug.
related: [other-post-slug]        # related articles (tops up by shared tags)
sources:                          # rendered References section
  - title: Google Search Central
    url: https://developers.google.com/search
---

# My Post Title

Direct answer paragraph first (featured snippet bait), then:

[TOC]   <!-- renders a linked table of contents -->

## Question-style H2s get anchor links automatically
```

Every field feeds the SEO layer automatically: meta title/description,
canonical URL, Open Graph article tags, BlogPosting + BreadcrumbList +
FAQPage JSON-LD, the Person author schema, and the sitemap. Only `title` is
really needed — everything else has fallbacks.

The per-site `author` block in `site.php` (name, title, bio, avatar, url,
`same_as` social links) powers the byline, the author bio box, and the Person
schema on every article — the E-E-A-T backbone.

### Publishing workflow

1. Edit or add a `.md` file (drafts preview in local dev)
2. Commit and push to `main`
3. `deploy.sh` runs `php artisan site:build --clean`, which renders every page
   to `public/static/{domain}/.../index.html`

### AI-drafted articles

With `ANTHROPIC_API_KEY` set, `site:write` drafts a complete article straight
into a site's content folder:

```bash
php artisan site:write myblog "How to choose a standing desk" \
    --keywords="standing desk, ergonomics" --words=1500
```

The draft follows an editorial skill from `resources/skills/` — the default is
`seo-article.md`, pick another with `--skill=product-review`, and any site can
override a skill with `resources/sites/{key}/skills/{skill}.md`. The default
skill enforces: search
intent match, a 40–60 word direct answer for the featured snippet, TL;DR,
[TOC], question-style H2s answered in the first sentence, data tables with
linked authority sources, internal links chosen from the site's existing
articles, image placeholders with `TODO screenshot` comments, FAQ, quiz, a
conclusion with one CTA, and a References list.

Posts are created with `draft: true`; drop in the screenshots, add any manual
in-text links, remove the flag, commit — or pass `--publish` to skip the
draft stage.

### Topic clusters

Single articles compete alone; clusters rank. `site:cluster` plans one
**pillar page** for a broad head keyword plus N **spoke articles** targeting
long-tail queries, all interlinked so the spokes push authority up to the
pillar:

```bash
php artisan site:cluster myblog "pergola"           # AI plans the cluster
php artisan site:cluster myblog pergola --write=3   # draft the next 3 articles
php artisan site:cluster myblog                     # progress across all clusters
```

The plan lands in `resources/sites/{key}/clusters/pergola.yaml` — review it,
prune spokes, tweak titles/keywords, then draft in batches. Spokes are
written first (each prompt tells the AI to link up to the pillar and to
published siblings); the pillar is drafted last so it can link to every spoke
that actually exists.

The links are also template-enforced: any post with `cluster: pergola` in its
front matter gets a "Part of our guide" box linking to the pillar, and the
pillar renders an "In this guide" list of its published spokes — so the
hub-and-spoke structure holds even where the article body forgot a link.

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

### Image pipeline

Drop images into `resources/sites/{key}/images/` and reference them from
markdown as `/images/foo.png` — nothing else. When the page renders, the
tag comes out as:

```html
<img src="/images/foo-800.webp"
     srcset="/images/foo-480.webp 480w, /images/foo-800.webp 800w, /images/foo-1600.webp 1600w"
     sizes="(max-width: 768px) 100vw, 720px"
     width="800" height="450" alt="..." loading="lazy" decoding="async">
```

WebP variants (typically 80–90% smaller than a raw screenshot), a responsive
`srcset` so phones never download desktop sizes, intrinsic dimensions so the
layout never shifts, and lazy loading — except the first image on the page
(the likely LCP), which gets `fetchpriority="high"` instead. That is Core
Web Vitals covered end to end.

`site:build` pre-generates every variant (incrementally — only new/changed
sources are reprocessed); a dynamic fallback route generates on demand, so
local dev needs no build step and a production cache miss heals itself.
SVG and GIF pass through untouched. Widths are whitelisted, so the dynamic
route can't be abused to generate arbitrary sizes.

### Multi-locale sites

A site becomes multi-locale by declaring extra locales in `site.php`:

```php
'locale' => 'en',        // default — served at the root (/)
'locales' => ['es'],     // extras — served under /es/...
```

Translations live in `content/{locale}/` with **matching slugs** — the
matching slug is what links two documents as translations:

```
content/blog/hello-world.md        -> /blog/hello-world       (en)
content/es/blog/hello-world.md     -> /es/blog/hello-world    (es)
content/es/pages/about.md          -> /es/about
```

What happens automatically for every translated document:

- `hreflang` alternates in the head (each locale + `x-default` pointing at
  the default locale) — the canonical multi-language SEO signal
- A language switcher in the site header (only on pages that actually have
  a translation)
- Per-locale sitemap entries and a per-locale RSS feed (`/es/feed.xml`)
- Localized UI chrome via `lang/{locale}.json` (`es` and `fr` ship with the
  template) and `lang="{locale}"` on `<html>`
- The static build renders every locale (`/es/...` output directories)

Untranslated documents simply don't exist in the other locale: no hreflang,
no switcher, a 404 under the prefix — no half-translated pages. Blade pages
(`views/pages/*.blade.php`) serve every locale with localized data, since
views are code rather than content.

### Deploying to Cloudflare Pages (serverless, no hub server)

Each site can be hosted on Cloudflare Pages instead of (or alongside) the
Caddy hub — free static hosting at the edge, no server load:

```bash
npx wrangler login          # once (or set CLOUDFLARE_API_TOKEN + CLOUDFLARE_ACCOUNT_ID)
php artisan site:deploy mysite
```

`site:deploy` builds the site with `--pages` and pushes it to a Pages
project named after the site key (creating the project on first run). The
`--pages` build emits two extra artifacts into the output:

- `_worker.js` — a serverless stand-in for the one dynamic endpoint the
  sites have: `POST /lead`. It mirrors `LeadController` (honeypot,
  validation, HMAC-SHA256-signed relay to the admin API).
- `_routes.json` — restricts worker invocation to `/lead`, so every other
  request is served as a pure static asset (no function invocations burned).

Every build (with or without `--pages`) also writes a branded `404.html`,
which Pages serves automatically for unknown paths.

Once per site, in the Cloudflare dashboard:

1. **Custom domains** — attach the site's domain(s) to the Pages project.
2. **Settings → Variables** — set `LEADS_WEBHOOK_URL` and
   `LEADS_WEBHOOK_SECRET` (the worker rejects leads without them; unlike
   the hub, Pages has no disk to park leads on).

Caveat vs the hub: on Pages there is no PHP fallback — everything must be
pre-built (which `site:build` does: pages, feeds, sitemaps, robots, search
index, all image variants), and if the admin API is down at submission
time the visitor sees the form's error message instead of a silent retry.

### Analytics (Umami)

Cookieless, GDPR-friendly, ~2 KB script — no consent banner needed. Each
static site sets its own Umami website id in `site.php` (`'analytics'`), the
main app uses `UMAMI_WEBSITE_ID` from the env; snippets are injected in
production only. Lead form submits fire a `lead-{form}` event automatically —
mark it as a goal in Umami to see which articles convert. Setup notes in
`DEPLOYMENT.md`.

### Error pages

`resources/views/errors/{404,500,503}.blade.php` render a minimal centered
status page. On a static site domain it carries that site's name and accent
color — and nothing that reveals the other sites in the project; everywhere
else it is just the code.

### Site search (Pagefind)

Set `'search' => true` in `site.php` and the site gets a `/search` page (plus
a nav link) powered by [Pagefind](https://pagefind.app): `site:build` indexes
the generated HTML and writes a static search bundle to
`public/static/{domain}/pagefind/`, which Caddy serves like any other static
file — no server, no database, ~100 KB of bandwidth per search. Only page
content is indexed (`data-pagefind-body` on `<main>`), the search page is
`noindex`, and the binary ships in the production image (dev uses the npm
package after `npm install`).

### Lead capture

Every site exposes `POST /lead` on its own domain — Caddy only serves GETs
from the static cache, so form submissions always reach PHP even on fully
cached pages. Nothing is stored in this project: a queued job relays the lead
to the admin project's API (`LEADS_WEBHOOK_URL`), retrying with backoff
(1m/5m/15m/1h) if it is down. Exhausted retries stay in `failed_jobs` with
the full payload — re-send with `php artisan queue:retry all`. With no
webhook configured, leads append to `storage/app/leads.jsonl` so they are
never dropped.

Enable the newsletter box under every article in `site.php`:

```php
'newsletter' => [
    'enabled' => true,
    'heading' => 'Get new articles by email',
    'text'    => 'One email per new article. No spam.',
    'button'  => 'Subscribe',
],
```

For contact forms (or any other lead magnet), include the same partial in any
Blade view:

```blade
@include('site::partials.lead-form', [
    'form' => 'contact',
    'heading' => 'Work with us',
    'button' => 'Send',
    'withName' => true,
    'withMessage' => true,
])
```

The form works on static HTML with no framework JS: it submits via `fetch`
(inline script, which also captures the live page path and `utm_*` params for
attribution) and falls back to a plain POST + redirect without JavaScript.
Spam is handled by a honeypot field and per-IP throttling (`LEADS_THROTTLE`,
default 10/min); the endpoint is CSRF-exempt because pre-rendered pages can't
carry a live token.

The relayed request is JSON, signed with HMAC-SHA256 over the exact body:

```json
{
  "id": "9c2f…", "site": "myblog", "domain": "myblog.com",
  "form": "newsletter", "email": "visitor@example.com",
  "name": null, "message": null, "page": "/blog/hello-world",
  "utm": {"utm_source": "twitter"}, "meta": {"ip": "…", "referrer": "…"},
  "created_at": "2026-07-04T09:00:00+00:00"
}
```

Verify it in the admin project (Laravel) before trusting the payload:

```php
// routes/api.php in the admin project
Route::post('/api/leads', function (Request $request) {
    $signature = hash_hmac('sha256', $request->getContent(), config('services.leads.secret'));

    abort_unless(hash_equals($signature, $request->header('X-Webhook-Signature', '')), 401);

    Lead::updateOrCreate(['uuid' => $request->json('id')], $request->json()->all());

    return response()->json(['ok' => true]);
});
```

The `X-Webhook-Id` header carries the lead's UUID — use it as the idempotency
key (retries re-send the same lead with the same id).

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

### Database backups

The `db-backup` sidecar (docker-compose.prod.yml) runs `pg_dump` daily and
rotates automatically: 7 daily, 4 weekly, 6 monthly dumps in `./backups/`
(git-ignored).

```bash
make prod-db-backup                    # take an extra backup right now
make prod-db-backups                   # list available dumps
make prod-db-restore FILE=backups/...  # restore (destructive, 5s to abort)
```

Backups on the same disk protect against bad migrations and fat-fingered
deletes — not against a dead server. Ship `./backups/` offsite with a nightly
`rclone sync` / `restic backup` cron to object storage (B2/S3), and do a
restore drill once after setting up.

## File Structure

```
app/
  Actions/CreateUser.php            # writes (one class per operation)
  Queries/UserIndexQuery.php        # reads (only layer that knows the schema)
  DTOs/UserData.php                 # immutable validated data between layers
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
