# Kami Template

Production-ready Laravel project bootstrapper with FrankenPHP, Docker, PostgreSQL, Redis, and automated CI/CD deployment.

## Quick Start

One command creates a fully configured Laravel project:

```bash
/var/www/kami-template/init.sh "My App" "myapp" "myapp.com" /var/www/myapp
```

That's it. The script will:

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

| # | Argument | Example | Used for |
|---|----------|---------|----------|
| 1 | App Name | `"PornGuru Cam"` | Display name, .env |
| 2 | App Slug | `"porngurucam"` | Docker containers, DB name, volumes |
| 3 | Domain | `"pornguru.cam"` | Caddyfile, HTTPS, session domain |
| 4 | Target Dir | `/var/www/porngurucam` | Where to create the project |

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
