# Agent Brief: Data Warehouse Content API + Performance + Monitoring

You are working on the **data warehouse** project (the central admin app). It is a Laravel 12
application built from our template, so the stack and conventions are fixed:

- PHP 8.2, Laravel 12, Postgres 16, Redis 7 (cache only — evictable, `allkeys-lru`), database queue
- FrankenPHP via Laravel Octane (long-running workers — no mutable static state, ever)
- Laravel Pulse + Telescope + Sentry are already installed
- Pest 3 for tests, Larastan, Pint
- Template conventions: writes live in `app/Actions/` (one class per operation), reads in
  `app/Queries/` (only layer that knows the schema), `app/DTOs/` for immutable data between layers.
  Follow them.
- Deploys via `deploy.sh` (docker compose): `app`, `worker`, `scheduler`, `db`, `redis` containers

## Context

The warehouse owns all content and data: photos, videos, users, comments, likes, favorites — and
now also **articles** and **SEO text blocks**. Multiple frontend sites (built from the same
template) render this content:

1. **Dynamic frontends** — their Laravel controllers call this API server-side (SSR; the browser
   never talks to the warehouse). They cache responses in Redis and invalidate via webhooks.
2. **Static sites** — fetched from this API **at build time** (`php artisan site:build`), output
   published to Cloudflare. No runtime dependency on the warehouse.
3. Some colocated frontends may read the database directly — that is allowed, but the API is the
   canonical contract and must stay complete enough that direct DB access is never *required*.

This API is critical infrastructure. Be precise. Everything below is a requirement, not a suggestion.

---

## 1. Content API (read side)

Base: `/api/v1/`. JSON only. All list endpoints paginated.

**Auth:** Laravel Sanctum personal access tokens (server-to-server bearer tokens, one per
frontend). Abilities: `read`, `write`. Read endpoints require `read`. No cookies, no sessions.

**Endpoints (minimum):**

```
GET /api/v1/articles?locale=es&updated_since=2026-08-01T00:00:00Z&cursor=...
GET /api/v1/articles/{slug}?locale=es
GET /api/v1/content-blocks/{key}?locale=es
GET /api/v1/photos?cursor=...&updated_since=...
GET /api/v1/videos?cursor=...
GET /api/v1/comments?commentable_type=video&commentable_id=123&cursor=...
GET /api/v1/health
```

**Rules:**

- **Articles** carry translations. Response includes all available locales with their slugs so
  consumers can build hreflang maps:
  ```json
  {
    "data": {
      "id": 12, "status": "published", "published_at": "...",
      "translations": {
        "en": { "slug": "hello-world", "title": "...", "excerpt": "...", "body": "...", "seo_title": null, "seo_description": null },
        "es": { "slug": "hola-mundo", "title": "...", "...": "..." }
      }
    }
  }
  ```
  `?locale=` filters body fields to one locale but still includes the `slug` of every other
  published translation.
- **Pagination:** cursor-based (`?cursor=`), 50 items default, 200 max. Cursor pagination must be
  stable under concurrent writes (order by `id`, not `updated_at`) — static builds paginate through
  the entire catalog and must not skip or duplicate rows.
- **`updated_since`** (ISO 8601) on every list endpoint for incremental syncs. Filter with
  `where('updated_at', '>', $ts)` — strictly greater, and document that consumers must store the
  *server* timestamp from the response meta, not their local clock.
- **ETag + 304:** every GET response carries an `ETag` (hash of the payload). Honor
  `If-None-Match` and return `304` with an empty body. Also send
  `Cache-Control: private, max-age=0, must-revalidate` (caching decisions belong to the consumer;
  the API itself is not publicly cacheable).
- **Server-Timing header on every API response** (see §4).
- **Errors:** consistent envelope `{ "error": { "code": "not_found", "message": "..." } }` with
  correct status codes (400/401/403/404/422/429). No HTML error pages under `/api/`.
- Use Laravel API Resources for output shaping. No raw model serialization — ever.

## 2. Write side (frontends → warehouse)

Likes, favorites, and comment submissions arrive from frontends:

```
POST /api/v1/likes        { "likeable_type": "video", "likeable_id": 123, "user_id": 456 }
POST /api/v1/comments     { "commentable_type": "video", "commentable_id": 123, "body": "..." }
```

- Require the `write` ability.
- **Idempotency:** clients send an `Idempotency-Key` header (UUID). Store keys for 24h (Redis)
  and replay the original response on duplicates instead of double-inserting. Unique DB
  constraints as the backstop (e.g. `unique(user_id, likeable_type, likeable_id)`).
- Rate limit writes tighter than reads (see §3).

## 3. Performance requirements

- **N+1:** `Model::preventLazyLoading(! app()->isProduction())` in a service provider. Every
  relation used by a Resource must be eager-loaded in the Query class. Larastan + a Pest test
  that hits each list endpoint and asserts query count stays constant regardless of result size
  (use `DB::enableQueryLog()` or the `expectsDatabaseQueryCount` approach).
- **Indexes:** every lookup column indexed — `articles(status, published_at)`,
  `article_translations(locale, slug)` unique, `comments(commentable_type, commentable_id, created_at)`,
  all `updated_at` columns used by `updated_since`. Write the migration with `->index()` calls and
  list them in the PR description.
- **Denormalized counters:** `likes_count`, `comments_count` columns on photos/videos/articles,
  maintained by model observers (or the write Actions). List endpoints must never run `COUNT(*)`
  per row.
- **Response caching:** cache hot read responses in Redis with tags:
  `Cache::tags(['articles'])->remember("api:article:{$slug}:{$locale}", 3600, ...)`.
  TTLs: article/content-block detail 1h, list endpoints 5min, counts 1min. All cached entries must
  be reachable by tag so §5 invalidation can flush precisely.
- **Octane-safe:** no request data in static properties or singletons; anything request-scoped is
  registered `->scoped()` or resolved per call. The template already does this for its own services
  — match that discipline.
- **Rate limiting:** `throttle:api` — 600/min per token for reads, 60/min for writes, keyed by
  token ID (not IP).
- **Payload size:** list Resources return summary fields only (no `body` in lists). gzip is
  handled by FrankenPHP/Caddy — verify `Content-Encoding: gzip` on a >1KB response in a test.

## 4. Monitoring (the owner wants to see request durations)

**Primary: Laravel Pulse** (already installed; dashboard is gated to localhost/VPN in production
by the template's `viewPulse` gate — keep that).

- Enable and configure recorders in `config/pulse.php`:
  - `SlowRequests` — threshold **200ms** for `/api/*` routes (API budget, not the 1s default)
  - `SlowQueries` — threshold **100ms**
  - `Cache`, `Queues`, `Servers`, `SlowJobs`, `Exceptions` — defaults
- **Custom API timing recorder:** a middleware on the `api` group that measures every request and
  records it to Pulse (`Pulse::record('api_request_duration', $routeName, $milliseconds)`), so the
  dashboard shows per-endpoint latency over time — not just slow outliers. Add a small custom Pulse
  card (Livewire component + view) rendering avg/p95 per endpoint. This is the headline feature:
  the owner opens `/pulse` and sees how long warehouse requests take to resolve.
- **Server-Timing middleware:** every API response includes
  `Server-Timing: app;dur={ms}` (total app time). This is also the consumer contract: frontends
  log this header to compare server-side time vs. their observed round-trip.
- **Structured request logs:** the same middleware logs one JSON line per API request:
  `{ "route": "articles.show", "status": 200, "duration_ms": 12.4, "queries": 3, "cached": true }`
  at `info` level to a dedicated `api` log channel (daily, 14 days).
- **Telescope:** local/dev only, never enabled in production (template already gates it).
- **Sentry:** set `SENTRY_TRACES_SAMPLE_RATE=0.1` in production env example; verify transactions
  appear for API routes.
- **`GET /api/v1/health`:** unauthenticated, returns `{ "db": "ok", "redis": "ok", "queue": "ok" }`
  with 200/503. Used by uptime monitoring.

## 5. Cache invalidation webhooks (warehouse → frontends)

Frontends cache API responses; the warehouse pushes invalidations on change. Mirror the existing
lead-webhook convention from the template exactly:

- A `webhook_subscriptions` table: `url`, `secret`, `events` (json array), `active`.
- On content create/update/delete (model observers on Article, ArticleTranslation, ContentBlock,
  Photo, Video, Comment), dispatch a **queued** job per active subscription:
  `DispatchContentInvalidationWebhook`.
- Payload:
  ```json
  {
    "id": "uuid-v4",
    "event": "article.updated",
    "type": "article",
    "slug": "hello-world",
    "locales": ["en", "es"],
    "sent_at": "2026-08-02T10:00:00Z"
  }
  ```
- Headers: `X-Webhook-Signature` = HMAC-SHA256 hex of the **exact raw request body** with the
  subscription secret; `X-Webhook-Id` = the payload `id`. (Consumers verify signature before
  trusting — same convention as the template's lead forwarding.)
- Job settings: `tries = 5`, `backoff = [60, 300, 900, 3600]`, `timeout(10)` on the HTTP call.
  Exhausted jobs land in `failed_jobs` and log an `error` line with the payload.
- Debounce: multiple changes to the same entity within 60s collapse into one webhook (check for a
  pending identical job via Redis lock before dispatching).

## 6. Build-time export support

Static sites fetch everything at build time. Make that fast and consistent:

- The `updated_since` + cursor pagination contract (§1) is the primary mechanism — a full build
  pages through the catalog; subsequent builds fetch only deltas.
- Add `GET /api/v1/export/manifest` returning per-content-type counts and max `updated_at`, so a
  build can cheaply decide "nothing changed, skip".
- Ensure a full-catalog export of 100k records completes without memory issues: chunk with cursor
  pagination, never `->all()`. Test with 10k seeded records and assert memory stays flat.

## 7. Consumer contract (documentation deliverable)

Write `docs/api-consumers.md` documenting everything a frontend needs: auth, endpoints, pagination,
`updated_since`, ETag usage, `Server-Timing`, error envelope, idempotency, rate limits, and the
webhook receiver spec (signature verification snippet, idempotent handling via `X-Webhook-Id`).
This document is the contract the frontend projects will be built against — it must match the
implementation exactly.

---

## Acceptance criteria

- [ ] All endpoints above exist, token-protected, with API Resources and the error envelope
- [ ] ETag/304 works (test: second request with `If-None-Match` returns 304)
- [ ] Query-count tests prove no N+1 on every list endpoint
- [ ] Cursor pagination stable test: insert rows mid-pagination, no skips/duplicates
- [ ] Pulse shows per-endpoint API durations; slow-request threshold 200ms on `/api/*`
- [ ] `Server-Timing` header present and accurate (±20ms) on API responses
- [ ] JSON request log lines written to the `api` channel
- [ ] Webhook: editing an article dispatches a signed job per subscription; signature verifies
      against the raw body; retries follow the backoff ladder; debounce collapses bursts
- [ ] Idempotent POST replay test passes
- [ ] `/api/v1/health` returns 200 with all checks ok, 503 when DB is down
- [ ] Export manifest endpoint returns correct counts
- [ ] `php artisan test` green, `vendor/bin/pint --test` clean, Larastan level unchanged
- [ ] `docs/api-consumers.md` written and matches the implementation

## Constraints

- Do not modify the existing admin UI/Filament/Livewire code except to add model observers.
- No new paid/external services. Everything runs in the existing docker-compose stack.
- No facades-in-DTOs, no logic in Resources beyond shaping, no raw DB queries outside `app/Queries/`.
- Octane safety: if you reach for a singleton, justify it in a code comment.
