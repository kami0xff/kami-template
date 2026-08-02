<?php

it('responds to the health check', function () {
    $this->get('/up')->assertOk();
});

it('renders the home page', function () {
    $this->get('/')->assertOk();
});

it('404s locale-prefixed URLs while only one locale is supported', function () {
    // config/locales.php ships with 'en' only, so the localized route group
    // is never registered. Enable a second locale there to activate /{locale}.
    $this->get('/es')->assertNotFound();
});

it('keeps OpenReplay off unless explicitly enabled', function () {
    $this->app['env'] = 'production';
    config(['services.openreplay.project_key' => 'test-key']);

    // A project key alone must not start session recording.
    $this->get('/')->assertOk()->assertDontSee('openreplay.js', false);

    config(['services.openreplay.enabled' => true]);

    $this->get('/')->assertOk()->assertSee('openreplay.js', false);
});
