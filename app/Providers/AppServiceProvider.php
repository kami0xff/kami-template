<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Prevent destructive database commands (db:wipe, migrate:fresh,
        // migrate:refresh, migrate:reset) from running in production, so a
        // stray command can never wipe live data. Non-production environments
        // (e.g. `make fresh` in local dev) are unaffected.
        DB::prohibitDestructiveCommands($this->app->isProduction());

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
        $allow = fn ($user = null) => ! $this->app->isProduction()
            || in_array(request()->getHost(), ['localhost', '127.0.0.1'], true);

        Gate::define('viewPulse', $allow);
        Gate::define('viewTelescope', $allow);
    }
}
