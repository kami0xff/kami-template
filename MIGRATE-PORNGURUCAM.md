# Migration Prompt: Standardize porngurucam Docker Setup

Copy this entire block and paste it as a prompt to the Claude agent working on `/var/www/porngurucam`.

---

## Prompt

Standardize the porngurucam Docker setup to match our project convention. The goal is consistent naming, structure, and services across all projects. Here's the exact target state:

### Container Naming Convention

ALL containers must follow `{slug}-{service}-{env}`:

**Production** (`docker-compose.prod.yml`):
- `porngurucam-app-prod` (FrankenPHP web server)
- `porngurucam-db-prod` (PostgreSQL)
- `porngurucam-redis-prod` (Redis)
- `porngurucam-worker-prod` (Queue worker)
- `porngurucam-scheduler-prod` (Laravel scheduler)

**Development** (`docker-compose.dev.yml`):
- `porngurucam-app-dev` (already correct)
- `porngurucam-scheduler-dev` (currently `porngurucam-scheduler`, add `-dev`)
- `porngurucam-db-dev` (currently `porngurucam-db`, add `-dev`)

### Changes Required

#### 1. `docker-compose.prod.yml`

Current state: has `app`, `db`, `redis` only. Container names are inconsistent (`porngurucam-app` without `-prod`, `porngurucam-redis` without `-prod`).

Update to:
- Add `-prod` suffix to ALL container names: `porngurucam-app-prod`, `porngurucam-db-prod`, `porngurucam-redis-prod`
- Add `worker` service: reuses the `porngurucam:production` image, runs `php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600`, container name `porngurucam-worker-prod`
- Add `scheduler` service: reuses the `porngurucam:production` image, runs `php artisan schedule:work --no-interaction`, container name `porngurucam-scheduler-prod`
- Remove caddy_data and caddy_config volumes (not needed with `auto_https off`)
- Remove the `SERVER_NAME` environment variable from app service (set in Caddyfile instead)
- Remove ports 443 and 443/udp from app (HTTP only behind Cloudflare Tunnel)
- Health check should use `http://127.0.0.1:80/up` not `http://localhost/up`
- Worker and scheduler both depend on `db` (healthy) and `redis` (healthy)
- Worker and scheduler share `app_storage` volume at `/app/storage`
- The compose name should be `pgcam-prod`
- DO NOT add a cloudflared container -- the tunnel runs as a system service

#### 2. `docker-compose.dev.yml`

- Rename `porngurucam-scheduler` to `porngurucam-scheduler-dev`
- Rename `porngurucam-db` to `porngurucam-db-dev`
- `porngurucam-app-dev` is already correct

#### 3. `Caddyfile`

Replace the current Caddyfile with a simplified single-block version:

```
{
    frankenphp
    auto_https off
    servers {
        trusted_proxies static private_ranges
    }
}

:80 {
    root * /app/public
    encode zstd gzip

    @www host www.pornguru.cam
    redir @www https://pornguru.cam{uri} permanent

    header {
        X-Frame-Options "SAMEORIGIN"
        X-Content-Type-Options "nosniff"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
        -Server
    }

    @build path /build/*
    header @build Cache-Control "public, max-age=31536000, immutable"

    @static path *.ico *.woff *.woff2 *.ttf *.eot
    header @static Cache-Control "public, max-age=86400"

    @images path *.jpg *.jpeg *.png *.gif *.svg *.webp
    header @images Cache-Control "public, max-age=604800"

    php_server

    log {
        output file /var/log/caddy/access.log {
            roll_size 100mb
            roll_keep 5
        }
        format json
    }
}
```

Key changes from current:
- Added `trusted_proxies static private_ranges` (gets real client IP behind Cloudflare)
- Single `:80` block instead of separate named host + catch-all (simpler, works the same because cloudflared passes the original Host header)
- Added `@images` cache rule for image assets

#### 4. `Dockerfile.frankenphp`

Add this line before the `chown` step to prevent dev-only service providers from being baked into the image:

```dockerfile
RUN ... \
    && rm -f bootstrap/cache/packages.php bootstrap/cache/services.php \
    && php artisan package:discover --ansi || true \
    && chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache
```

#### 5. `.dockerignore`

Make sure `bootstrap/cache/*.php` is listed (prevents dev package cache from leaking into production image).

#### 6. `deploy.sh`

Update the health check to use HTTP (not HTTPS) since auto_https is off:

```bash
HTTP_STATUS=$(${COMPOSE} exec -T app curl -sf -o /dev/null -w "%{http_code}" http://127.0.0.1:80/up 2>/dev/null || echo "000")
```

Also add after migrations:
```bash
${COMPOSE} exec -T app php artisan storage:link 2>/dev/null || true
```

### After making changes

1. Rebuild: `docker compose --env-file .env.production -f docker-compose.prod.yml build app`
2. Restart: `docker compose --env-file .env.production -f docker-compose.prod.yml up -d --remove-orphans`
3. Verify all containers have `-prod` suffix: `docker ps --filter "name=porngurucam" --format "table {{.Names}}\t{{.Status}}"`

### Reference

The hhentai project at `/var/www/hhentai` is the canonical example of this convention. Files to reference:
- `docker-compose.prod.yml`
- `docker-compose.dev.yml` (dev naming convention)
- `Dockerfile.frankenphp`
- `Caddyfile`
- `deploy.sh`
