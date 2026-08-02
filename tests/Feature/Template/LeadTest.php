<?php

use App\Jobs\ForwardLeadToWebhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

it('queues a lead for forwarding on form submission', function () {
    Queue::fake();

    $this->postJson('http://example.test/lead', [
        'form' => 'newsletter',
        'email' => 'Visitor@Example.com',
        'page' => '/blog/hello-world',
        'utm_source' => 'twitter',
    ])->assertCreated()->assertJson(['ok' => true]);

    Queue::assertPushed(ForwardLeadToWebhook::class, function (ForwardLeadToWebhook $job) {
        return $job->payload['site'] === 'example'
            && $job->payload['form'] === 'newsletter'
            && $job->payload['email'] === 'visitor@example.com'
            && $job->payload['page'] === '/blog/hello-world'
            && $job->payload['utm'] === ['utm_source' => 'twitter']
            && !empty($job->payload['id']);
    });
});

it('redirects non-JS submissions back with a thanks flag', function () {
    Queue::fake();

    $this->post('http://example.test/lead', [
        'email' => 'visitor@example.com',
    ], ['Referer' => 'https://example.test/blog/hello-world'])
        ->assertRedirect('https://example.test/blog/hello-world?lead=thanks');
});

it('silently drops honeypot submissions', function () {
    Queue::fake();

    $this->postJson('http://example.test/lead', [
        'email' => 'bot@example.com',
        'website' => 'https://spam.example',
    ])->assertCreated();

    Queue::assertNothingPushed();
});

it('rejects an invalid email', function () {
    Queue::fake();

    $this->postJson('http://example.test/lead', ['email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');

    Queue::assertNothingPushed();
});

it('deduplicates repeat signups within an hour', function () {
    Queue::fake();

    $this->postJson('http://example.test/lead', ['email' => 'twice@example.com'])->assertCreated();
    $this->postJson('http://example.test/lead', ['email' => 'twice@example.com'])->assertCreated();

    Queue::assertPushed(ForwardLeadToWebhook::class, 1);
});

it('forwards the lead to the admin webhook with a valid signature', function () {
    config([
        'leads.webhook.url' => 'https://admin.example.com/api/leads',
        'leads.webhook.secret' => 'test-secret',
    ]);
    Http::fake(['admin.example.com/*' => Http::response(['ok' => true])]);

    (new ForwardLeadToWebhook([
        'id' => 'lead-uuid',
        'site' => 'example',
        'form' => 'newsletter',
        'email' => 'visitor@example.com',
    ]))->handle();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://admin.example.com/api/leads'
            && $request->header('X-Webhook-Id')[0] === 'lead-uuid'
            && $request->header('X-Webhook-Signature')[0]
                === hash_hmac('sha256', $request->body(), 'test-secret')
            && $request->header('Content-Type')[0] === 'application/json';
    });
});

it('parks leads locally when no webhook is configured', function () {
    config(['leads.webhook.url' => null]);
    Storage::fake();

    (new ForwardLeadToWebhook([
        'id' => 'lead-uuid',
        'email' => 'visitor@example.com',
    ]))->handle();

    Storage::assertExists('leads.jsonl');
    expect(Storage::get('leads.jsonl'))->toContain('visitor@example.com');
});

it('parks the lead locally when the webhook fails on the sync queue', function () {
    config(['leads.webhook.url' => 'https://admin.example.com/api/leads']);
    Http::fake(['admin.example.com/*' => Http::response('down', 503)]);
    Storage::fake();

    // Sync dispatch (lean hub: no worker) — the visitor request must succeed.
    $this->postJson('http://example.test/lead', ['email' => 'parked@example.com'])
        ->assertCreated();

    Storage::assertExists('leads.jsonl');
    expect(Storage::get('leads.jsonl'))->toContain('parked@example.com');
});

it('renders the newsletter form on blog posts when enabled', function () {
    $this->get('http://example.test/blog/hello-world')
        ->assertOk()
        ->assertSee('data-lead-form', false)
        ->assertSee('action="/lead"', false)
        ->assertSee('name="website"', false); // honeypot baked into the form
});
