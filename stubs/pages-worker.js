/**
 * Cloudflare Pages worker for a static site deployed with `site:deploy`.
 * Written to the build output as _worker.js by `site:build --pages`.
 *
 * Only the /lead endpoint runs here (_routes.json limits invocation to it;
 * everything else is served directly from static assets). It mirrors the
 * hub's LeadController: honeypot, validation, HMAC-SHA256-signed relay to
 * the admin project's API.
 *
 * Configure on the Pages project (Settings -> Variables, or
 * `wrangler pages secret put`):
 *   LEADS_WEBHOOK_URL     admin API endpoint
 *   LEADS_WEBHOOK_SECRET  shared HMAC secret
 *
 * NOTE: unlike the hub, Pages has no disk to park leads on when the admin
 * API is down — the visitor sees the form's error message and can retry.
 */

const SITE_KEY = '__SITE_KEY__';
const SITE_DOMAIN = '__SITE_DOMAIN__';

export default {
    async fetch(request, env) {
        const url = new URL(request.url);

        if (url.pathname === '/lead' && request.method === 'POST') {
            return handleLead(request, env);
        }

        return env.ASSETS.fetch(request);
    },
};

async function handleLead(request, env) {
    const fields = await parseBody(request);

    // Honeypot: hidden field filled means bot — pretend success.
    if (fields.website) {
        return success(request);
    }

    const email = String(fields.email || '').trim().toLowerCase();

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.length > 255) {
        return new Response(JSON.stringify({ message: 'Invalid email.' }), {
            status: 422,
            headers: { 'content-type': 'application/json' },
        });
    }

    if (!env.LEADS_WEBHOOK_URL || !env.LEADS_WEBHOOK_SECRET) {
        console.error('LEADS_WEBHOOK_URL / LEADS_WEBHOOK_SECRET not configured on this Pages project — lead rejected.');

        return failure();
    }

    const payload = {
        id: crypto.randomUUID(),
        site: SITE_KEY,
        domain: SITE_DOMAIN,
        form: /^[a-z0-9\-_]{1,50}$/.test(fields.form || '') ? fields.form : 'newsletter',
        email,
        name: truncate(fields.name, 120),
        message: truncate(fields.message, 5000),
        page: truncate(fields.page, 500) || refererPath(request),
        utm: utm(fields),
        meta: prune({
            ip: request.headers.get('cf-connecting-ip'),
            user_agent: truncate(request.headers.get('user-agent'), 500),
            referrer: request.headers.get('referer'),
        }),
        created_at: new Date().toISOString(),
    };

    const body = JSON.stringify(payload);

    const response = await fetch(env.LEADS_WEBHOOK_URL, {
        method: 'POST',
        headers: {
            'content-type': 'application/json',
            'x-webhook-signature': await hmacHex(env.LEADS_WEBHOOK_SECRET, body),
            'x-webhook-id': payload.id,
        },
        body,
    });

    if (!response.ok) {
        console.error(`Lead forward failed: admin API answered HTTP ${response.status}`);

        return failure();
    }

    return success(request);
}

async function parseBody(request) {
    const type = request.headers.get('content-type') || '';

    if (type.includes('application/json')) {
        return await request.json().catch(() => ({}));
    }

    const form = await request.formData().catch(() => null);

    return form ? Object.fromEntries(form.entries()) : {};
}

function success(request) {
    if ((request.headers.get('accept') || '').includes('application/json')) {
        return new Response(JSON.stringify({ ok: true }), {
            status: 201,
            headers: { 'content-type': 'application/json' },
        });
    }

    // No-JS fallback: back to the static page with the thank-you flag.
    const back = request.headers.get('referer') || '/';
    const separator = back.includes('?') ? '&' : '?';

    return Response.redirect(back + separator + 'lead=thanks', 303);
}

function failure() {
    // The lead form's inline script shows its error message on non-2xx.
    return new Response(JSON.stringify({ ok: false }), {
        status: 502,
        headers: { 'content-type': 'application/json' },
    });
}

function refererPath(request) {
    const referer = request.headers.get('referer');

    if (!referer) return null;

    try {
        return new URL(referer).pathname;
    } catch {
        return null;
    }
}

function utm(fields) {
    const keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    const out = {};

    for (const key of keys) {
        if (typeof fields[key] === 'string' && fields[key] !== '') {
            out[key] = fields[key].slice(0, 255);
        }
    }

    return out;
}

function truncate(value, max) {
    return typeof value === 'string' && value !== '' ? value.slice(0, max) : null;
}

function prune(object) {
    return Object.fromEntries(Object.entries(object).filter(([, v]) => v));
}

async function hmacHex(secret, body) {
    const key = await crypto.subtle.importKey(
        'raw',
        new TextEncoder().encode(secret),
        { name: 'HMAC', hash: 'SHA-256' },
        false,
        ['sign'],
    );

    const signature = await crypto.subtle.sign('HMAC', key, new TextEncoder().encode(body));

    return [...new Uint8Array(signature)].map((b) => b.toString(16).padStart(2, '0')).join('');
}
