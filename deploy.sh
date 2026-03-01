#!/bin/bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

COMPOSE="docker compose --env-file .env.production -f docker-compose.prod.yml"

log()  { echo -e "${BLUE}[deploy]${NC} $1"; }
ok()   { echo -e "${GREEN}[  ok  ]${NC} $1"; }
warn() { echo -e "${YELLOW}[ warn ]${NC} $1"; }
err()  { echo -e "${RED}[error ]${NC} $1"; }

# Pre-flight
log "Starting deployment..."

if [ ! -f ".env.production" ]; then
    err ".env.production not found"
    exit 1
fi

# Pull latest code
if [ -d ".git" ]; then
    log "Pulling latest from origin/main..."
    git fetch origin
    git checkout main
    git pull origin main
    ok "Code updated"
fi

# Build
log "Building FrankenPHP production image..."
${COMPOSE} build app
ok "Image built"

# Start
log "Starting containers..."
${COMPOSE} up -d --remove-orphans
ok "Containers started"

# Wait for DB
log "Waiting for database..."
for i in $(seq 1 30); do
    if ${COMPOSE} exec -T db pg_isready > /dev/null 2>&1; then
        ok "Database ready"
        break
    fi
    [ "$i" -eq 30 ] && { err "Database timeout"; exit 1; }
    sleep 1
done

# Migrate
log "Running migrations..."
${COMPOSE} exec -T app php artisan migrate --force
ok "Migrations done"

# Optimize
log "Optimizing..."
${COMPOSE} exec -T app php artisan optimize:clear
${COMPOSE} exec -T app php artisan optimize
${COMPOSE} exec -T app php artisan view:cache
${COMPOSE} exec -T app php artisan event:cache 2>/dev/null || true
ok "Caches built"

# Storage link
${COMPOSE} exec -T app php artisan storage:link 2>/dev/null || true

# Health check
log "Health check..."
sleep 5
HTTP_STATUS=$(${COMPOSE} exec -T app curl -sf -o /dev/null -w "%{http_code}" http://127.0.0.1:80/up 2>/dev/null || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    ok "Health check passed (HTTP ${HTTP_STATUS})"
else
    warn "Health check returned HTTP ${HTTP_STATUS}"
    echo "  Check logs: ${COMPOSE} logs -f app"
fi

echo ""
${COMPOSE} ps
echo ""
ok "Deployment complete!"
echo ""
echo "  Logs:  ${COMPOSE} logs -f"
echo "  App:   ${COMPOSE} logs -f app"
echo ""
