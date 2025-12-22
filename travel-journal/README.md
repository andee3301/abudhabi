# Travel Journal (Laravel)

Laravel 12 + Livewire 3 playground for planning trips, journaling, and sharing media. The project ships with a dashboard, trip management, an API secured by Sanctum, and marketing assets that can be served locally or via CDN.

## Quickstart
- Requirements: PHP 8.2+, Composer, Node 20+, SQLite/MySQL, Redis (queue/cache recommended).
- Install: `composer install && npm install`
- Env: `cp .env.example .env` then set `APP_URL`, database, queue, mail, `SANCTUM_STATEFUL_DOMAINS`, `TELESCOPE_ENABLED=false` by default.
- Migrate & seed: `php artisan migrate --seed`
- Serve: `npm run dev` (Vite) and `php artisan serve` or use `composer dev`.
- Tests: `php artisan test` (aim for >70% coverage).

## Architecture
- **Frontend**: Blade + Livewire 3 (Volt), Tailwind. Dashboard and trip flows live in `resources/views` + `app/Livewire`.
- **API**: REST under `/api` guarded by Sanctum tokens with abilities; requests live in `app/Http/Requests`, resources in `app/Http/Resources`.
- **Domain models**: Trips, Itinerary Items, Journal Entries, Country Visits, Marketing Assets. See `database/migrations` and `app/Models`.
- **Queues**: Async jobs (weather snapshots, media processing) via Laravel queue workers; configure `QUEUE_CONNECTION` and retry strategy in `config/queue.php`.
- **Observability**: Telescope (guarded by env + gate), Pulse metrics, optional Sentry/Slack notifications.

## API & Auth
- Issue tokens: `POST /api/auth/token` with `email`, `password`, `device_name`, optional `abilities` (e.g., `["trips:read","trips:write","itinerary:write","journal:write","stats:read"]`).
- Protected routes require abilities (e.g., `/api/trips` needs `trips:read`, writes need `trips:write`; journal/itinerary endpoints have their own scopes).
- API docs: `/docs/api` via Scramble (requires auth); exports OpenAPI to `api.json`.

## Benchmarks (targets)
- App boot (cold) < 350ms, dashboard TTFB < 200ms when cached.
- Dashboard queries cached for 5 minutes; slow queries logged at >300ms.
- Lighthouse (authenticated dashboard) target 90+ performance, <200KB critical CSS/JS per page after code-splitting.
- API p95 latency < 150ms for trip CRUD; >70% test coverage enforced in CI.

## Developer Workflow
- **Branching**: `main` is stable; create feature branches as `feature/{issue-id}-slug` or `chore/{issue-id}-slug`; hotfixes as `hotfix/{issue-id}-slug`.
- **Commits**: Prefix with issue id and type, e.g., `PROJ-123 feat: add timezone selectors`. See `CONTRIBUTING.md`.
- **PRs**: Open against `main`, fill PR template, link issues. Run tests and linters before opening.
- **GitHub issues**: See `docs/issue-queue.md` for pre-drafted benchmark issues with acceptance criteria.

## Configuration Keys
- `MARKETING_ASSET_CDN`: Optional CDN base for marketing assets.
- `OPENWEATHER_API_KEY`: Weather snapshots.
- `SENTRY_LARAVEL_DSN` (if enabled), `SLACK_BOT_USER_OAUTH_TOKEN` for critical alerts.
- `TELESCOPE_ENABLED`, `TELESCOPE_ALLOWED_EMAILS`, `PULSE_ENABLED`: Toggle observability in different environments.
- `SLOW_QUERY_THRESHOLD_MS`: Logs DB queries above the threshold to `slow_query`.

## Scripts
- `composer setup`: One-shot local setup (deps, env, migrations, npm build).
- `composer dev`: Run server, queue listener, logs, and Vite concurrently.
- `composer production`: Cache config/routes/views/events, run migrations, and build assets for a production deploy (use `--skip-asset`
  and `--skip-migrate` flags on `app:ready` if needed).
- `npm run build`: Production assets with code-splitting and CSS pruning.

## Testing Strategy
- Feature + API tests for trip CRUD, itinerary, journal, auth; Livewire component tests for dashboard widgets and trip planner; unit tests for repositories and jobs.
- Async jobs include retry/backoff scenarios; error paths asserted.

## Deployment Notes
- Enable `APP_ENV=production`, `APP_DEBUG=false`, configure cache/queue drivers (Redis), run `php artisan config:cache route:cache view:cache`.
- Serve assets via CDN where possible; ensure `MARKETING_ASSET_CDN` points to uploaded marketing assets.
- Configure Sentry/Slack credentials before enabling alerts; expose Telescope only to authorized users.

## Documentation
- Marketing asset sources and licensing: `docs/marketing-assets.md`.
- Curated sources for SVGs, photography, video, and audio: `docs/media-sources.md`.
