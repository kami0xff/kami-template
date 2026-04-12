# Kami Template

Production-ready Laravel starter with built-in SEO infrastructure and multi-locale support.

## Stack

- PHP 8.3 + Laravel 12
- FrankenPHP (production) / PHP dev server (local)
- PostgreSQL 16, Redis 7
- Docker Compose, GitHub Actions CI/CD

## Quick Start

```bash
# 1. Create a new project from this template
git clone <this-repo> /var/www/my-project
cd /var/www/my-project

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.production.example .env
php artisan key:generate

# 4. Run migrations
php artisan migrate

# 5. Start development server
php artisan serve
```

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
