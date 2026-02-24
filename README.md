# Laravel FrankenPHP Template

Production-ready Laravel template with FrankenPHP, Docker, PostgreSQL, Redis, and automated CI/CD deployment.

## What's Included

| File | Purpose |
|------|---------|
| `Dockerfile.frankenphp` | Multi-stage production build (Composer + Node + FrankenPHP) |
| `docker-compose.prod.yml` | Production stack (App + PostgreSQL + Redis) |
| `docker-compose.dev.yml` | Development stack with Vite HMR + scheduler |
| `Caddyfile` | HTTP server config (Cloudflare Tunnel ready) |
| `deploy.sh` | Zero-downtime deployment script |
| `Makefile` | Dev & prod shortcuts + git workflow helpers |
| `.github/workflows/deploy.yml` | GitHub Actions CI/CD (SSH deploy) |
| `.env.production.example` | Production environment template |
| `init.sh` | One-time setup script to replace placeholders |

## Quick Start

### 1. Clone the template

```bash
git clone https://github.com/YOUR_ORG/laravel-template.git my-new-app
cd my-new-app
```

### 2. Initialize with your project details

```bash
chmod +x init.sh
./init.sh "My App Name" "myapp" "myapp.com"
```

This replaces all `__APP_NAME__`, `__APP_SLUG__`, and `__APP_DOMAIN__` placeholders across every config file.

### 3. Install Laravel

```bash
composer install
php artisan key:generate
```

### 4. Start development

```bash
make dev
# App at http://localhost:8787
# Vite at http://localhost:5173
```

### 5. Deploy to production

```bash
cp .env.production.example .env.production
# Fill in your values (APP_KEY, DB_PASSWORD, etc.)
make prod
```

## Stack

- **PHP 8.3** via FrankenPHP (Caddy embedded)
- **Laravel 12**
- **PostgreSQL 16** (Alpine)
- **Redis 7** (Alpine)
- **Vite** for frontend asset bundling
- **Docker Compose** for orchestration
- **GitHub Actions** for CI/CD

See [DEPLOYMENT.md](DEPLOYMENT.md) for full deployment documentation.
