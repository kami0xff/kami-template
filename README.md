# Kami Template

Production-ready Laravel project bootstrapper with FrankenPHP, Docker, PostgreSQL, Redis, and automated CI/CD deployment.

## Quick Start

One command creates a fully configured Laravel project:

```bash
/var/www/kami-template/init.sh "HHentai" /var/www/hhentai
```

That's it. The script auto-derives the slug (`hhentai`) and domain (`hhentai.com`) from the name.

If your domain doesn't match `slug.com`, pass it as a third argument:

```bash
/var/www/kami-template/init.sh "PornGuru Cam" /var/www/porngurucam pornguru.cam
```

The script will:

1. Install a fresh Laravel 12 project
2. Configure PostgreSQL + Redis
3. Install the FrankenPHP Docker deployment stack
4. Set up CI/CD, Makefile, deploy.sh, Caddyfile
5. Create a welcome page with environment indicator
6. Initialize a git repository

Then just:

```bash
cd /var/www/myapp
make dev          # Start dev at http://localhost:8787
```

## Arguments

| # | Argument | Required | Example | Description |
|---|----------|----------|---------|-------------|
| 1 | App Name | Yes | `"HHentai"` | Display name — slug is auto-derived (lowercase, no spaces) |
| 2 | Target Dir | Yes | `/var/www/hhentai` | Where to create the project |
| 3 | Domain | No | `pornguru.cam` | Defaults to `slug.com` — override if different |

## What's in the box

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

## Stack

- **PHP 8.3** via FrankenPHP (Caddy embedded)
- **Laravel 12**
- **PostgreSQL 16** (Alpine)
- **Redis 7** (Alpine)
- **Vite** for frontend assets
- **Docker Compose** for orchestration
- **GitHub Actions** for CI/CD
