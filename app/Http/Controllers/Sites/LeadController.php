<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use App\Jobs\ForwardLeadToWebhook;
use App\Services\Sites\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Receives form submissions from static sites (POST /lead on the site's own
 * domain — Caddy only serves GETs from the static cache, so POSTs always
 * reach PHP even on fully cached pages).
 *
 * Leads are not stored in this project: the payload is handed to a queued
 * job that relays it to the admin project's API (see ForwardLeadToWebhook).
 *
 * The endpoint is CSRF-exempt (pre-rendered pages can't carry a live token);
 * it is protected instead by a honeypot field, per-IP throttling, and the
 * fact that the site is resolved from the Host header, not from user input.
 */
class LeadController extends Controller
{
    public function store(Request $request, Site $site)
    {
        // Honeypot: the "website" field is visually hidden; anything filling
        // it is a bot. Pretend success so the bot doesn't adapt.
        if ($request->filled('website')) {
            return $this->success($request);
        }

        $data = $request->validate([
            'form' => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9\-_]+$/'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'name' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:5000'],
            'page' => ['nullable', 'string', 'max:500'],
        ]);

        $form = $data['form'] ?? 'newsletter';
        $email = mb_strtolower(trim($data['email']));

        // Idempotency: double submits / repeat newsletter signups within an
        // hour are dropped via a cache marker (contact messages always go
        // through). Cache::add is atomic — returns false if the key exists.
        $fresh = !empty($data['message'])
            || Cache::add("lead:{$site->key}:{$form}:" . sha1($email), true, now()->addHour());

        if ($fresh) {
            ForwardLeadToWebhook::dispatch([
                'id' => (string) Str::uuid(),
                'site' => $site->key,
                'domain' => $site->canonicalDomain(),
                'form' => $form,
                'email' => $email,
                'name' => $data['name'] ?? null,
                'message' => $data['message'] ?? null,
                'page' => $data['page'] ?? $this->refererPath($request),
                'utm' => $this->utm($request),
                'meta' => array_filter([
                    'ip' => $request->ip(),
                    'user_agent' => Str::limit($request->userAgent() ?? '', 500) ?: null,
                    'referrer' => $request->headers->get('referer'),
                ]),
                'created_at' => now()->toIso8601String(),
            ]);
        }

        return $this->success($request);
    }

    protected function success(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true], 201);
        }

        // No-JS fallback: back to the (static) page with a flag the form's
        // inline script reads to show the thank-you message.
        $back = $request->headers->get('referer') ?: '/';
        $back .= (str_contains($back, '?') ? '&' : '?') . 'lead=thanks';

        return redirect()->to($back);
    }

    /** Path of the page the form was on, from the Referer header. */
    protected function refererPath(Request $request): ?string
    {
        $referer = $request->headers->get('referer');

        return $referer ? (parse_url($referer, PHP_URL_PATH) ?: '/') : null;
    }

    /** utm_* params captured by the form's inline script at submit time. */
    protected function utm(Request $request): array
    {
        return collect($request->only([
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        ]))
            ->filter(fn($v) => is_string($v) && $v !== '')
            ->map(fn($v) => mb_substr($v, 0, 255))
            ->all();
    }
}
