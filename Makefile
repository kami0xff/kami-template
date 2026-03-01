# ==============================================
# __APP_NAME__ — Dev & Prod Commands
# ==============================================
# Usage: make help
# ==============================================

.DEFAULT_GOAL := help

# Compose commands
DEV  = docker compose -f docker-compose.dev.yml
PROD = docker compose --env-file .env.production -f docker-compose.prod.yml

.PHONY: help dev dev-down dev-logs dev-shell prod prod-up prod-down prod-logs prod-shell \
        build migrate fresh seed test lint lint-fix tinker cache-clear \
        artisan queue-work dev-db-shell prod-db-shell ps feature finish release

# ==============================================
# Help
# ==============================================

help:
	@echo ""
	@echo "  Development"
	@echo "  ─────────────────────────────────────"
	@echo "  make dev            Start dev stack"
	@echo "  make dev-down       Stop dev stack"
	@echo "  make dev-logs       Tail dev logs"
	@echo "  make dev-shell      Shell into dev app"
	@echo ""
	@echo "  Database & Artisan"
	@echo "  ─────────────────────────────────────"
	@echo "  make migrate        Run migrations (dev)"
	@echo "  make fresh          Fresh migrate + seed (dev)"
	@echo "  make seed           Run seeders (dev)"
	@echo "  make tinker         Open Laravel Tinker (dev)"
	@echo "  make artisan c=...  Run artisan command (dev)"
	@echo "  make dev-db-shell   Open psql shell (dev)"
	@echo "  make cache-clear    Clear all caches (dev)"
	@echo ""
	@echo "  Testing & Quality"
	@echo "  ─────────────────────────────────────"
	@echo "  make test           Run tests"
	@echo "  make lint           Check code style"
	@echo "  make lint-fix       Fix code style"
	@echo ""
	@echo "  Production"
	@echo "  ─────────────────────────────────────"
	@echo "  make prod           Full deploy (build + restart + migrate + cache)"
	@echo "  make build          Build prod image only"
	@echo "  make prod-up        Start prod (no rebuild)"
	@echo "  make prod-down      Stop prod stack"
	@echo "  make prod-logs      Tail prod logs"
	@echo "  make prod-shell     Shell into prod app"
	@echo "  make prod-db-shell  Open psql shell (prod)"
	@echo "  make queue-work     Start queue worker (prod)"
	@echo "  make ps             List all containers"
	@echo ""
	@echo "  Git Workflow"
	@echo "  ─────────────────────────────────────"
	@echo "  make feature F=name   Create feature branch from dev"
	@echo "  make finish           Merge current feature → dev"
	@echo "  make release          Merge dev → main + deploy"
	@echo ""

# ==============================================
# Development
# ==============================================

dev:
	$(DEV) up -d
	@echo ""
	@echo "  Dev running at http://localhost:8787"
	@echo ""

dev-down:
	$(DEV) down

dev-logs:
	$(DEV) logs -f app

dev-shell:
	$(DEV) exec app sh

# ==============================================
# Database & Artisan (dev)
# ==============================================

migrate:
	$(DEV) exec -T app php artisan migrate

fresh:
	$(DEV) exec -T app php artisan migrate:fresh --seed

seed:
	$(DEV) exec -T app php artisan db:seed

tinker:
	$(DEV) exec app php artisan tinker

artisan:
	$(DEV) exec -T app php artisan $(c)

dev-db-shell:
	$(DEV) exec db psql -U __APP_SLUG__ -d __APP_SLUG__

cache-clear:
	$(DEV) exec -T app php artisan optimize:clear

# ==============================================
# Testing & Quality
# ==============================================

test:
	$(DEV) exec -T app php artisan test

lint:
	$(DEV) exec -T app ./vendor/bin/pint --test

lint-fix:
	$(DEV) exec -T app ./vendor/bin/pint

# ==============================================
# Production
# ==============================================

prod:
	./deploy.sh

build:
	$(PROD) build app

prod-up:
	$(PROD) up -d --remove-orphans

prod-down:
	$(PROD) down

prod-logs:
	$(PROD) logs -f app

prod-shell:
	$(PROD) exec app sh

prod-db-shell:
	$(PROD) exec db psql -U __APP_SLUG__ -d __APP_SLUG__

queue-work:
	$(PROD) exec app php artisan queue:work redis --sleep=3 --tries=3

ps:
	@echo "── Dev ──"
	@$(DEV) ps 2>/dev/null || echo "  (not running)"
	@echo ""
	@echo "── Prod ──"
	@$(PROD) ps 2>/dev/null || echo "  (not running)"

# ==============================================
# Git Workflow
# ==============================================

feature:
ifndef F
	@echo "Usage: make feature F=my-feature-name"
	@exit 1
endif
	git checkout dev
	git pull origin dev
	git checkout -b feature/$(F)
	@echo "Created feature/$(F) from dev"

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

release:
	@echo "Merging dev → main..."
	git checkout main
	git pull origin main
	git merge dev
	git push origin main
	git checkout dev
	@echo ""
	@echo "  Pushed to main."
	@echo "  Run: make prod"
	@echo ""
