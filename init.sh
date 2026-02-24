#!/bin/bash
# ==============================================
# Kami Template — Full Project Bootstrapper
#
# Creates a complete Laravel project with the
# production deployment stack in one command.
#
# Usage:
#   ./init.sh "My App Name" "myapp" "myapp.com" /var/www/myapp
#
# Arguments:
#   1. APP_NAME   — Human-readable name (e.g. "My Cool App")
#   2. APP_SLUG   — Lowercase slug for Docker/DB (e.g. "myapp")
#   3. APP_DOMAIN — Production domain (e.g. "myapp.com")
#   4. TARGET_DIR — Where to create the project (e.g. /var/www/myapp)
# ==============================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log()  { echo -e "${BLUE}[init]${NC} $1"; }
ok()   { echo -e "  ${GREEN}✓${NC} $1"; }
warn() { echo -e "  ${YELLOW}!${NC} $1"; }
err()  { echo -e "${RED}[error]${NC} $1"; }

TEMPLATE_DIR="$(cd "$(dirname "$0")" && pwd)"

# --------------------------------------------------
# Validate arguments
# --------------------------------------------------
if [ "$#" -lt 4 ]; then
    echo ""
    echo -e "${BLUE}Kami Template${NC} — Full Project Bootstrapper"
    echo ""
    echo -e "  ${GREEN}Usage:${NC}"
    echo "    ./init.sh \"App Name\" \"app-slug\" \"domain.com\" /var/www/app-slug"
    echo ""
    echo -e "  ${GREEN}Example:${NC}"
    echo "    ./init.sh \"PornGuru Cam\" \"porngurucam\" \"pornguru.cam\" /var/www/porngurucam"
    echo "    ./init.sh \"HHentai\" \"hhentai\" \"hhentai.com\" /var/www/hhentai"
    echo ""
    echo -e "  ${GREEN}What it does:${NC}"
    echo "    1. Creates a fresh Laravel 12 project"
    echo "    2. Configures PostgreSQL + Redis"
    echo "    3. Installs FrankenPHP Docker deployment stack"
    echo "    4. Sets up CI/CD, Makefile, deploy.sh"
    echo "    5. Adds welcome page with environment indicator"
    echo "    6. Initializes git repository"
    echo ""
    exit 1
fi

APP_NAME="$1"
APP_SLUG="$2"
APP_DOMAIN="$3"
TARGET_DIR="$4"

echo ""
log "Creating project..."
echo -e "  Name:   ${GREEN}${APP_NAME}${NC}"
echo -e "  Slug:   ${GREEN}${APP_SLUG}${NC}"
echo -e "  Domain: ${GREEN}${APP_DOMAIN}${NC}"
echo -e "  Path:   ${GREEN}${TARGET_DIR}${NC}"
echo ""

# --------------------------------------------------
# Pre-flight checks
# --------------------------------------------------
if [ -d "${TARGET_DIR}" ] && [ "$(ls -A "${TARGET_DIR}" 2>/dev/null)" ]; then
    err "Target directory ${TARGET_DIR} already exists and is not empty."
    exit 1
fi

command -v composer >/dev/null 2>&1 || { err "composer is required but not installed."; exit 1; }
command -v git >/dev/null 2>&1 || { err "git is required but not installed."; exit 1; }

# --------------------------------------------------
# Step 1: Create fresh Laravel project
# --------------------------------------------------
log "Installing Laravel 12..."
mkdir -p "$(dirname "${TARGET_DIR}")"
composer create-project laravel/laravel "${TARGET_DIR}" --prefer-dist --quiet
ok "Laravel installed"

cd "${TARGET_DIR}"

# Remove default SQLite database
rm -f database/database.sqlite

# --------------------------------------------------
# Step 2: Copy deployment files from template
# --------------------------------------------------
log "Installing deployment stack..."

DEPLOY_FILES=(
    "Dockerfile.frankenphp"
    "Caddyfile"
    "docker-compose.prod.yml"
    "docker-compose.dev.yml"
    "deploy.sh"
    "Makefile"
    ".env.production.example"
    ".dockerignore"
    "DEPLOYMENT.md"
)

for file in "${DEPLOY_FILES[@]}"; do
    if [ -f "${TEMPLATE_DIR}/${file}" ]; then
        cp "${TEMPLATE_DIR}/${file}" "${TARGET_DIR}/${file}"
    fi
done

mkdir -p "${TARGET_DIR}/.github/workflows"
cp "${TEMPLATE_DIR}/.github/workflows/deploy.yml" "${TARGET_DIR}/.github/workflows/deploy.yml"
cp "${TEMPLATE_DIR}/public/robots.txt" "${TARGET_DIR}/public/robots.txt"

chmod +x "${TARGET_DIR}/deploy.sh"
ok "Deployment files copied"

# --------------------------------------------------
# Step 3: Replace placeholders
# --------------------------------------------------
log "Configuring for ${APP_NAME}..."

TEMPLATED_FILES=(
    "Caddyfile"
    "docker-compose.prod.yml"
    "docker-compose.dev.yml"
    "Makefile"
    ".env.production.example"
    "DEPLOYMENT.md"
)

for file in "${TEMPLATED_FILES[@]}"; do
    if [ -f "$file" ]; then
        sed -i "s/__APP_NAME__/${APP_NAME}/g" "$file"
        sed -i "s/__APP_SLUG__/${APP_SLUG}/g" "$file"
        sed -i "s/__APP_DOMAIN__/${APP_DOMAIN}/g" "$file"
    fi
done
ok "Placeholders replaced"

# --------------------------------------------------
# Step 4: Configure .env for PostgreSQL
# --------------------------------------------------
log "Configuring database..."

cat > .env <<ENVEOF
APP_NAME="${APP_NAME}"
APP_ENV=local
APP_KEY=$(grep '^APP_KEY=' .env.backup 2>/dev/null | cut -d= -f2- || php artisan key:generate --show 2>/dev/null || echo "")
APP_DEBUG=true
APP_URL=http://localhost:8787

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=${APP_SLUG}
DB_USERNAME=${APP_SLUG}
DB_PASSWORD=changeme

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@${APP_DOMAIN}"
MAIL_FROM_NAME="\${APP_NAME}"

VITE_APP_NAME="\${APP_NAME}"
ENVEOF

# Ensure APP_KEY is set
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --ansi 2>/dev/null || true
fi

ok "PostgreSQL + Redis configured"

# --------------------------------------------------
# Step 5: Add Docker entries to .gitignore
# --------------------------------------------------
log "Updating .gitignore..."

cat >> .gitignore <<'GIEOF'

# Docker volumes data
/data/
/logs/

# Sitemaps (generated at runtime)
/public/sitemap*.xml

# SQLite dev database
/database/*.sqlite
GIEOF

ok ".gitignore updated"

# --------------------------------------------------
# Step 6: Create welcome page with env indicator
# --------------------------------------------------
log "Creating welcome page..."

cat > resources/views/welcome.blade.php <<'VIEWEOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0a;
            color: #e5e5e5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { text-align: center; padding: 2rem; max-width: 480px; }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, #888 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .badge-local {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .badge-production {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .badge-staging {
            background: rgba(234, 179, 8, 0.15);
            color: #facc15;
            border: 1px solid rgba(234, 179, 8, 0.3);
        }
        .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        .dot-local { background: #4ade80; }
        .dot-production { background: #f87171; }
        .dot-staging { background: #facc15; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .info {
            display: grid; gap: 0.75rem; text-align: left;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
            padding: 1.25rem; font-size: 0.8125rem;
        }
        .info-row { display: flex; justify-content: space-between; align-items: center; }
        .info-label { color: #888; }
        .info-value {
            color: #e5e5e5; font-weight: 500;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }
        .info-row + .info-row {
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .subtitle { color: #666; font-size: 0.8125rem; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ config('app.name') }}</h1>
        @php
            $env = app()->environment();
            $badgeClass = match($env) {
                'production' => 'badge-production',
                'staging' => 'badge-staging',
                default => 'badge-local',
            };
            $dotClass = match($env) {
                'production' => 'dot-production',
                'staging' => 'dot-staging',
                default => 'dot-local',
            };
        @endphp
        <div class="badge {{ $badgeClass }}">
            <span class="dot {{ $dotClass }}"></span>
            {{ $env }}
        </div>
        <div class="info">
            <div class="info-row">
                <span class="info-label">Environment</span>
                <span class="info-value">{{ $env }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Debug</span>
                <span class="info-value">{{ config('app.debug') ? 'ON' : 'OFF' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">PHP</span>
                <span class="info-value">{{ PHP_VERSION }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Laravel</span>
                <span class="info-value">{{ app()->version() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Database</span>
                <span class="info-value">{{ config('database.default') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cache</span>
                <span class="info-value">{{ config('cache.default') }}</span>
            </div>
        </div>
        <p class="subtitle">Ready to build something great.</p>
    </div>
</body>
</html>
VIEWEOF

ok "Welcome page created"

# --------------------------------------------------
# Step 7: Initialize git
# --------------------------------------------------
log "Initializing git..."

git init --quiet
git branch -m main 2>/dev/null || true
git add -A
git commit -m "Initial project: ${APP_NAME} — Laravel $(php artisan --version 2>/dev/null | grep -oP '[\d.]+' || echo '12') + FrankenPHP

Fresh project created from kami-template with:
- FrankenPHP multi-stage Docker build
- PostgreSQL 16 + Redis 7
- Docker Compose for dev and prod
- Zero-downtime deploy.sh
- GitHub Actions CI/CD
- Welcome page with environment indicator" --quiet

ok "Git initialized (branch: main)"

# --------------------------------------------------
# Done
# --------------------------------------------------
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  Project created successfully!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "  Path:   ${TARGET_DIR}"
echo -e "  Domain: ${APP_DOMAIN}"
echo ""
echo -e "  ${BLUE}Development:${NC}"
echo "    cd ${TARGET_DIR}"
echo "    make dev"
echo "    # App at http://localhost:8787"
echo ""
echo -e "  ${BLUE}Production:${NC}"
echo "    cp .env.production.example .env.production"
echo "    nano .env.production   # Set APP_KEY, DB_PASSWORD"
echo "    make prod"
echo ""
echo -e "  ${BLUE}CI/CD:${NC}"
echo "    Add GitHub secrets (see DEPLOYMENT.md)"
echo ""
