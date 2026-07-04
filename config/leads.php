<?php

/**
 * Lead Capture (static sites -> admin project)
 *
 * Every form submission on a static site POSTs to /lead on the site's own
 * domain. Nothing is stored in this project: a queued job relays the signed
 * payload to the admin project's API, retrying with backoff if it is down.
 * Exhausted retries sit in failed_jobs (queue:retry all re-sends them);
 * with no webhook URL configured, leads append to storage/app/leads.jsonl.
 */

return [

    'webhook' => [
        // Admin project endpoint that receives leads, e.g.
        // https://admin.example.com/api/leads
        'url' => env('LEADS_WEBHOOK_URL'),

        // Shared secret used to sign the JSON body (HMAC-SHA256, sent as
        // the X-Webhook-Signature header). Must match the admin project.
        'secret' => env('LEADS_WEBHOOK_SECRET'),
    ],

    // Max submissions per minute per IP on the /lead endpoint.
    'throttle' => env('LEADS_THROTTLE', 10),

];
