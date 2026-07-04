# ==============================================
# Template - Dev & Prod Commands
# ==============================================
# Usage:
#   make dev        - Start development environment
#   make prod       - Deploy production
#   make logs       - Tail production logs
#   make help       - Show all commands
# ==============================================

.PHONY: help configure setup dev dev-up dev-down dev-logs dev-shell prod prod-up prod-down prod-logs prod-shell migrate fresh test build ide-helper boost-install boost-update assets assets-build site-new sites-build \
	prod-status prod-stats prod-health prod-logs-laravel prod-logs-access prod-slow-requests prod-logs-worker prod-logs-scheduler prod-queue prod-schedule prod-frankenphp-process-list prod-db-activity prod-db-connections prod-db-slow-queries prod-db-stats-reset prod-redis prod-telescope-prune

# Default
help:
	@echo ""
	@echo "  Project Setup (run once after cloning)"
	@echo "  ─────────────────────────────────────"
	@echo "  make configure NAME=\"My App\" SLUG=myapp DOMAIN=myapp.com"
	@echo "  make setup        Bootstrap everything (env, containers, deps, key, migrate, assets)"
	@echo ""
	@echo "  Development"
	@echo "  ─────────────────────────────────────"
	@echo "  make dev          Start dev stack"
	@echo "  make dev-down     Stop dev stack"
	@echo "  make dev-logs     Tail dev logs"
	@echo "  make dev-shell    Shell into dev app"
	@echo "  make migrate      Run migrations (dev)"
	@echo "  make fresh        Fresh migrate + seed (dev)"
	@echo "  make test         Run tests"
	@echo "  make assets       Build assets in watch mode (vite build --watch)"
	@echo "  make assets-build One-off production asset build"
	@echo "  make ide-helper   Generate IDE helper files"
	@echo "  make boost-install  Install Laravel Boost (MCP + guidelines)"
	@echo "  make boost-update   Refresh Boost guidelines/skills"
	@echo ""
	@echo "  Static Sites"
	@echo "  ─────────────────────────────────────"
	@echo "  make site-new KEY=myblog DOMAIN=myblog.com   Scaffold a new site"
	@echo "  make sites-build                             Build static snapshots"
	@echo ""
	@echo "  Production"
	@echo "  ─────────────────────────────────────"
	@echo "  make prod         Full production deploy"
	@echo "  make prod-up      Start prod (no rebuild)"
	@echo "  make prod-down    Stop prod stack"
	@echo "  make prod-logs    Tail prod logs"
	@echo "  make prod-shell   Shell into prod app"
	@echo "  make build        Build prod image only"
	@echo ""
	@echo "  Monitoring (production)"
	@echo "  ─────────────────────────────────────"
	@echo "  make prod-status       Container health / restarts"
	@echo "  make prod-stats        Live CPU/memory per container"
	@echo "  make prod-health       /up check + artisan about"
	@echo "  make prod-logs-laravel Tail Laravel app log"
	@echo "  make prod-logs-access  Tail HTTP access log (JSON)"
	@echo "  make prod-slow-requests Slow (>1s) requests (needs jq)"
	@echo "  make prod-logs-worker  Queue worker output"
	@echo "  make prod-logs-scheduler Scheduler output"
	@echo "  make prod-queue        Pending backlog + failed jobs"
	@echo "  make prod-schedule     Scheduled tasks + next runs"
	@echo "  make prod-db-activity  Active Postgres queries"
	@echo "  make prod-db-connections  Postgres connections by state"
	@echo "  make prod-db-slow-queries Top queries by total time (pg_stat_statements)"
	@echo "  make prod-db-stats-reset  Reset pg_stat_statements counters"
	@echo "  make prod-redis        Redis hit/miss + memory"
	@echo "  make prod-telescope-prune Trim Telescope data now"
	@echo "  Dashboards: /pulse (always-on)  /telescope (debug; off in prod)"
	@echo ""
	@echo "  Git Workflow"
	@echo "  ─────────────────────────────────────"
	@echo "  make feature F=name  Create feature branch"
	@echo "  make finish          Merge current feature → dev"
	@echo "  make release         Merge dev → main (deploy)"
	@echo ""

# ==============================================
# Project Setup
# ==============================================

# Replace template placeholders with your project's values across config files.
# SLUG must be lowercase (used for Docker names, DB name, composer package).
# Usage: make configure NAME="My App" SLUG=myapp DOMAIN=myapp.com
configure:
	@if [ -z "$(NAME)" ] || [ -z "$(SLUG)" ] || [ -z "$(DOMAIN)" ]; then \
		echo "Usage: make configure NAME=\"My App\" SLUG=myapp DOMAIN=myapp.com"; \
		exit 1; \
	fi
	@for f in docker-compose.dev.yml docker-compose.prod.yml \
	          Caddyfile .env.example .env.production.example .cursor/mcp.json DEPLOYMENT.md; do \
		[ -f "$$f" ] && sed -i \
			-e "s|__APP_NAME__|$(NAME)|g" \
			-e "s|__APP_SLUG__|$(SLUG)|g" \
			-e "s|__APP_DOMAIN__|$(DOMAIN)|g" "$$f" && echo "  configured $$f"; \
	done
	@echo ""
	@echo "Done. Next:  make setup"
	@echo ""

# One-shot bootstrap after `make configure` + `cp .env.example .env`:
# starts the dev stack, installs deps, generates the app key, migrates,
# and builds front-end assets. Idempotent — safe to re-run.
setup:
	@if [ ! -f .env ]; then cp .env.example .env && echo "  created .env from .env.example"; fi
	docker compose -f docker-compose.dev.yml up -d --build
	docker compose -f docker-compose.dev.yml exec app composer install
	docker compose -f docker-compose.dev.yml exec app sh -c 'grep -q "^APP_KEY=base64:" .env || php artisan key:generate'
	docker compose -f docker-compose.dev.yml exec app php artisan migrate
	docker compose -f docker-compose.dev.yml exec app sh -c "[ -d node_modules ] || npm ci; npm run build"
	@echo ""
	@echo "  Ready — app running at http://localhost:8787"
	@echo ""

# ==============================================
# tinker
# ==============================================
tinker-dev:
	docker compose -f docker-compose.dev.yml exec app php artisan tinker

tinker-prod:
	$(DC_PROD) exec app php artisan tinker



# ==============================================
# Development
# ==============================================

dev:
	docker compose -f docker-compose.dev.yml up -d
	@echo ""
	@echo "  Dev running at http://localhost:8787"
	@echo "  Build assets:  make assets   (vite build --watch)"
	@echo ""

dev-down:
	docker compose -f docker-compose.dev.yml down

dev-logs:
	docker compose -f docker-compose.dev.yml logs -f app

dev-shell:
	docker compose -f docker-compose.dev.yml exec app sh

migrate:
	docker compose -f docker-compose.dev.yml exec app php artisan migrate

dev-frankenphp-process-list:
	docker compose -f docker-compose.dev.yml exec app ps -T 1

fresh:
	docker compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed

test:
	docker compose -f docker-compose.dev.yml exec app php artisan test

# Scaffold a new static site: make site-new KEY=myblog DOMAIN=myblog.com
site-new:
	@if [ -z "$(KEY)" ] || [ -z "$(DOMAIN)" ]; then \
		echo "Usage: make site-new KEY=myblog DOMAIN=myblog.com"; \
		exit 1; \
	fi
	docker compose -f docker-compose.dev.yml exec app php artisan site:make $(KEY) $(DOMAIN) --with-www

# Render all static sites to public/static/{domain} (Caddy serves these in prod)
sites-build:
	docker compose -f docker-compose.dev.yml exec app php artisan site:build --clean

# Rebuild assets into public/build on every change (no dev server / HMR).
assets:
	docker compose -f docker-compose.dev.yml exec app sh -c "[ -d node_modules ] || npm ci; npm run dev"

# One-off asset build (same output the production image produces).
assets-build:
	docker compose -f docker-compose.dev.yml exec app sh -c "[ -d node_modules ] || npm ci; npm run build"

ide-helper:
	docker compose -f docker-compose.dev.yml exec app php artisan ide-helper:generate
	docker compose -f docker-compose.dev.yml exec app php artisan ide-helper:models --nowrite
	docker compose -f docker-compose.dev.yml exec app php artisan ide-helper:meta

# Install Laravel Boost (interactive: select agents/guidelines).
# The MCP server itself is pre-wired for Cursor in .cursor/mcp.json (Docker-routed),
# so deselect/ignore any host-php MCP entry Boost offers to write.
boost-install:
	docker compose -f docker-compose.dev.yml exec app php artisan boost:install

# Refresh Boost AI guidelines/skills to match installed package versions.
boost-update:
	docker compose -f docker-compose.dev.yml exec app php artisan boost:update

# ==============================================
# Production
# ==============================================

prod:
	./deploy.sh

build:
	docker compose --env-file .env.production -f docker-compose.prod.yml build

prod-up:
	docker compose --env-file .env.production -f docker-compose.prod.yml up -d

prod-down:
	docker compose --env-file .env.production -f docker-compose.prod.yml down

prod-logs:
	docker compose --env-file .env.production -f docker-compose.prod.yml logs -f app

prod-shell:
	docker compose --env-file .env.production -f docker-compose.prod.yml exec app sh

# ==============================================
# Monitoring & Observability (production)
# ==============================================

DC_PROD := docker compose --env-file .env.production -f docker-compose.prod.yml

# Container health: status, uptime, restart counts (start here)
prod-status:
	$(DC_PROD) ps

# Live CPU / memory / IO for all prod containers (Ctrl-C to exit).
# Watch the app container's MEM USAGE for worker memory creep.
prod-stats:
	docker stats $$($(DC_PROD) ps -q)

# Health endpoint + framework/cache/Octane snapshot
prod-health:
	@$(DC_PROD) exec app curl -sf http://127.0.0.1/up && echo "  /up OK"
	$(DC_PROD) exec app php artisan about

# Tail the Laravel app log (LOG_LEVEL=warning in prod, so errors/warnings)
prod-logs-laravel:
	$(DC_PROD) exec app tail -f storage/logs/laravel.log

# Tail the Caddy/FrankenPHP JSON access log (every HTTP request)
prod-logs-access:
	$(DC_PROD) exec app tail -f /var/log/caddy/access.log

# Slowest requests (>1s) from the last 1000 access-log lines.
# NOTE: requires `jq` on the HOST (the log is piped out of the container).
prod-slow-requests:
	$(DC_PROD) exec app tail -n 1000 /var/log/caddy/access.log | jq -c 'select(.duration > 1) | {status, dur: .duration, uri: .request.uri}'

# Queue worker / scheduler container output
prod-logs-worker:
	$(DC_PROD) logs -f --tail=50 worker

prod-logs-scheduler:
	$(DC_PROD) logs -f --tail=50 scheduler

# Queue health: pending backlog count + failed jobs
prod-queue:
	@echo "Pending jobs:"
	@$(DC_PROD) exec app php artisan tinker --execute="echo DB::table('jobs')->count().PHP_EOL;"
	@echo "Failed jobs:"
	@$(DC_PROD) exec app php artisan queue:failed

# Registered scheduled tasks + next run times
prod-schedule:
	$(DC_PROD) exec app php artisan schedule:list

# FrankenPHP process/thread view (Go threads + php-N workers)
prod-frankenphp-process-list:
	docker exec -it porngurucam-app-prod ps -T 1

# Postgres targets read POSTGRES_USER/POSTGRES_DB from the db container's own
# environment, so they work for any project slug without configuration.

# Postgres: active (non-idle) queries, longest-running first
prod-db-activity:
	$(DC_PROD) exec db sh -c 'psql -U $$POSTGRES_USER -d $$POSTGRES_DB -c "SELECT pid, state, now()-query_start AS runtime, left(query,80) AS query FROM pg_stat_activity WHERE state <> '"'"'idle'"'"' ORDER BY runtime DESC;"'

# Postgres: connection counts by state (Octane holds persistent conns)
prod-db-connections:
	$(DC_PROD) exec db sh -c 'psql -U $$POSTGRES_USER -d $$POSTGRES_DB -c "SELECT count(*), state FROM pg_stat_activity GROUP BY state;"'

# Postgres: most expensive queries by total time (needs pg_stat_statements)
prod-db-slow-queries:
	$(DC_PROD) exec db sh -c 'psql -U $$POSTGRES_USER -d $$POSTGRES_DB -c "SELECT calls, round(total_exec_time::numeric,1) AS total_ms, round(mean_exec_time::numeric,2) AS mean_ms, left(query,70) AS query FROM pg_stat_statements ORDER BY total_exec_time DESC LIMIT 15;"'

# Reset collected pg_stat_statements counters (start a fresh measurement window)
prod-db-stats-reset:
	$(DC_PROD) exec db sh -c 'psql -U $$POSTGRES_USER -d $$POSTGRES_DB -c "SELECT pg_stat_statements_reset();"'

# Trim Telescope data on demand (scheduler also prunes daily)
prod-telescope-prune:
	$(DC_PROD) exec app php artisan telescope:prune --hours=48

# Redis: cache hit/miss, evictions, and memory usage
prod-redis:
	@$(DC_PROD) exec redis redis-cli INFO stats | grep -E "keyspace_(hits|misses)|evicted_keys"
	@$(DC_PROD) exec redis redis-cli INFO memory | grep -E "used_memory_human|maxmemory_human"

# ==============================================
# Git Workflow Helpers
# ==============================================

# Create a feature branch off dev
# Usage: make feature F=stream-quality-fix
feature:
ifndef F
	@echo "Usage: make feature F=my-feature-name"
	@exit 1
endif
	git checkout dev
	git pull origin dev
	git checkout -b feature/$(F)
	@echo "Created feature/$(F) from dev"

# Merge current feature branch back into dev
finish:
	$(eval BRANCH := $(shell git branch --show-current))
	@if echo "$(BRANCH)" | grep -q "^feature/"; then \
		git checkout dev && \
		git pull origin dev && \
		git merge $(BRANCH) && \
		git push origin dev && \
		git branch -d $(BRANCH) && \
		echo "Merged $(BRANCH) → dev"; \
	else \
		echo "Not on a feature branch (current: $(BRANCH))"; \
		exit 1; \
	fi

# Merge dev into main for production release
release:
	@echo "Merging dev → main..."
	git checkout main
	git pull origin main
	git merge dev
	git push origin main
	git checkout dev
	@echo ""
	@echo "Pushed to main. CI/CD will deploy automatically."
	@echo "Or run: make prod"
	@echo ""
