<?php

namespace App\Providers;

use App\Services\Sites\SiteRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteRegistry::class);
    }

    public function boot(): void
    {
        // Prevent destructive database commands (db:wipe, migrate:fresh,
        // migrate:refresh, migrate:reset) from running in production, so a
        // stray command can never wipe live data. Non-production environments
        // (e.g. `make fresh` in local dev) are unaffected.
        DB::prohibitDestructiveCommands($this->app->isProduction());

        // Pristine copy of the seo config, captured before any request could
        // mutate it. SetSite derives each site's seo config from this so that
        // long-lived processes (Octane workers, site:build) never leak one
        // site's overrides into another's.
        $this->app->instance('seo.defaults', config('seo'));

        // Default `site::` view namespace (the shared templates). SetSite
        // re-registers it per request with the current site's own views
        // first; registering the fallback at boot means site:: views also
        // resolve outside a site request (artisan, static analysis).
        View::addNamespace('site', resource_path('views/sites'));

        // Lead capture endpoint (POST /lead on static site domains): the
        // form is public and CSRF-exempt, so cap submissions per IP.
        RateLimiter::for('leads', fn(Request $request) => Limit::perMinute(
            (int) config('leads.throttle', 10)
        )->by($request->ip()));

        $this->authorizeDashboards();
    }

    /**
     * Authorize access to the Pulse and Telescope dashboards.
     *
     * This template ships without an auth/users table, so access is decided by
     * how the app is reached rather than by login:
     *   - Non-production (local/dev): always allowed.
     *   - Production: allowed only when the request Host is localhost/127.0.0.1,
     *     i.e. reached over the VPN via an SSH port-forward to the container.
     *     Public traffic comes through the Cloudflare tunnel with the real
     *     domain as Host, so it is denied here (and 404'd in the Caddyfile).
     */
    private function authorizeDashboards(): void
    {
        $allow = fn($user = null) => !$this->app->isProduction()
            || in_array(request()->getHost(), ['localhost', '127.0.0.1'], true);

        Gate::define('viewPulse', $allow);
        Gate::define('viewTelescope', $allow);
    }
}
