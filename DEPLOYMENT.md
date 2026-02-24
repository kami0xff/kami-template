# Deployment Guide — __APP_NAME__

## Git Workflow

```
feature/xyz  ──→  dev  ──→  main (production)
                   ↑          ↑
              test here    auto-deploys
```

| Branch | Purpose | Deploys to |
|--------|---------|------------|
| `main` | Production-ready code | __APP_DOMAIN__ (auto via CI/CD) |
| `dev` | Integration & testing | Local dev stack |
| `feature/*` | New features / fixes | Nothing |

### Daily workflow

```bash
# 1. Start a feature
make feature F=fix-something

# 2. Work, commit, push
git add -A && git commit -m "fix: something"
git push -u origin feature/fix-something

# 3. When done, merge into dev
make finish

# 4. Test on dev
make dev

# 5. When ready, release to production
make release
```

## First-Time Server Setup

### 1. Clone and checkout

```bash
git clone git@github.com:YOUR_ORG/YOUR_REPO.git /var/www/__APP_SLUG__
cd /var/www/__APP_SLUG__
```

### 2. Configure production environment

```bash
cp .env.production.example .env.production
nano .env.production
```

**Required values:**
- `APP_KEY` — Generate: `docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"`
- `DB_PASSWORD` — Strong password for PostgreSQL

### 3. Deploy

```bash
./deploy.sh
```

This builds the Docker image, starts containers, runs migrations, and builds caches.

### 4. Point DNS

Add DNS records for your domain:

```
__APP_DOMAIN__      A    YOUR_SERVER_IP
www.__APP_DOMAIN__  A    YOUR_SERVER_IP
```

Or if using Cloudflare Tunnel, configure the tunnel to point to `http://localhost:80`.

## Commands

```bash
make help          # Show all available commands

# Development
make dev           # Start dev stack (port 8787)
make dev-down      # Stop dev stack
make dev-logs      # Tail dev logs
make test          # Run tests

# Production
make prod          # Full deploy (build + restart + migrate + cache)
make prod-logs     # Tail production logs
make prod-shell    # Shell into production container
make build         # Build image only (no restart)
```

## Manual deployment

```bash
git checkout main
git pull origin main
./deploy.sh
```

## CI/CD (GitHub Actions)

Pushing to `main` triggers the pipeline automatically.

### Required GitHub Secrets

Go to **Settings → Secrets and variables → Actions** and add:

| Secret | Value |
|--------|-------|
| `SSH_HOST` | Your server IP |
| `SSH_PORT` | SSH port (default: 22) |
| `SSH_USERNAME` | SSH user |
| `SSH_PRIVATE_KEY` | Contents of `~/.ssh/id_ed25519` (private key) |
| `DEPLOY_PATH` | `/var/www/__APP_SLUG__` |

## Architecture

```
Internet
   │
   ▼
┌──────────────────────────────────────┐
│  FrankenPHP (Caddy + PHP 8.3)        │
│  Automatic HTTPS · HTTP/2 · HTTP/3   │
│  Ports: 80, 443                       │
├──────────────────────────────────────┤
│  Laravel Application                  │
│  OPcache · Config/Route/View cache    │
├──────────────────────────────────────┤
│  PostgreSQL 16  │  Redis 7            │
│  Database       │  Cache/Queue        │
└──────────────────────────────────────┘
```

## Troubleshooting

```bash
# Check container status
docker compose -f docker-compose.prod.yml ps

# Check health
docker inspect __APP_SLUG__-app | grep -A 10 "Health"

# View all logs
docker compose -f docker-compose.prod.yml logs -f

# Restart everything
docker compose -f docker-compose.prod.yml restart

# Clear all caches
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
```
