#!/bin/bash
# ==============================================
# Initialize a new project from this template
#
# Usage:
#   ./init.sh "My App Name" "my-app" "myapp.com"
#
# Arguments:
#   1. APP_NAME   — Human-readable name (e.g. "My App Name")
#   2. APP_SLUG   — Lowercase slug for Docker/DB (e.g. "my-app" or "myapp")
#   3. APP_DOMAIN — Production domain (e.g. "myapp.com")
# ==============================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

if [ "$#" -lt 3 ]; then
    echo -e "${RED}Usage:${NC} ./init.sh \"App Name\" \"app-slug\" \"domain.com\""
    echo ""
    echo "  Example: ./init.sh \"PornGuru Cam\" \"porngurucam\" \"pornguru.cam\""
    echo ""
    exit 1
fi

APP_NAME="$1"
APP_SLUG="$2"
APP_DOMAIN="$3"

echo -e "${BLUE}[init]${NC} Initializing project..."
echo -e "  Name:   ${GREEN}${APP_NAME}${NC}"
echo -e "  Slug:   ${GREEN}${APP_SLUG}${NC}"
echo -e "  Domain: ${GREEN}${APP_DOMAIN}${NC}"
echo ""

# Files that contain placeholders
FILES=(
    "Caddyfile"
    "docker-compose.prod.yml"
    "docker-compose.dev.yml"
    "Makefile"
    ".env.production.example"
    "composer.json"
    "DEPLOYMENT.md"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        sed -i "s/__APP_NAME__/${APP_NAME}/g" "$file"
        sed -i "s/__APP_SLUG__/${APP_SLUG}/g" "$file"
        sed -i "s/__APP_DOMAIN__/${APP_DOMAIN}/g" "$file"
        echo -e "  ${GREEN}✓${NC} $file"
    fi
done

# Create .env from example if it doesn't exist
if [ ! -f ".env" ]; then
    cat > .env <<EOF
APP_NAME="${APP_NAME}"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8787

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=${APP_SLUG}
DB_USERNAME=${APP_SLUG}
DB_PASSWORD=changeme

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

LOG_CHANNEL=stack
LOG_LEVEL=debug

VITE_APP_NAME="\${APP_NAME}"
EOF
    echo -e "  ${GREEN}✓${NC} .env created"
fi

# Make scripts executable
chmod +x deploy.sh
echo -e "  ${GREEN}✓${NC} deploy.sh made executable"

echo ""
echo -e "${GREEN}[done]${NC} Project initialized!"
echo ""
echo "  Next steps:"
echo "  ─────────────────────────────────────────────"
echo "  1. Install Laravel:  composer install"
echo "  2. Generate key:     php artisan key:generate"
echo "  3. Start dev:        make dev"
echo "  4. For production:   cp .env.production.example .env.production"
echo "                       nano .env.production"
echo "                       make prod"
echo ""
echo "  CI/CD: Add GitHub secrets (see DEPLOYMENT.md)"
echo ""

# Self-destruct — this script is only needed once
rm -f init.sh
echo -e "  ${YELLOW}init.sh removed (one-time use)${NC}"
echo ""
