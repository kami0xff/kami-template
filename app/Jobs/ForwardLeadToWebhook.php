<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Relays one lead to the admin project's API (config/leads.php). This
 * project stores nothing: the payload travels inside the queued job, so a
 * down admin API costs retries — never the lead. Exhausted retries land in
 * failed_jobs (payload included) and are re-sent with `queue:retry all`.
 *
 * On the sync queue (lean static-hub deployments with no worker or
 * database) there is no retry ladder and no failed_jobs table, so a failed
 * forward parks the lead in storage/app/leads.jsonl instead of failing the
 * visitor's request. Import the file once the admin API is reachable.
 *
 * The JSON body is signed with HMAC-SHA256 over the exact bytes sent, using
 * the shared secret — the admin project must verify the X-Webhook-Signature
 * header before trusting the payload (snippet in the README).
 */
class ForwardLeadToWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var array<int> Seconds between retries: 1m, 5m, 15m, 1h. */
    public array $backoff = [60, 300, 900, 3600];

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        $url = config('leads.webhook.url');
        $body = json_encode($this->payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if (empty($url)) {
            // No admin API configured (yet): park the lead in a local file so
            // it is never silently dropped. Import it once the API exists.
            Storage::append('leads.jsonl', $body);
            Log::warning('Lead captured but LEADS_WEBHOOK_URL is not set; appended to storage leads.jsonl', [
                'lead' => $this->payload['id'] ?? null,
            ]);

            return;
        }

        try {
            Http::withHeaders([
                'X-Webhook-Signature' => hash_hmac('sha256', $body, (string) config('leads.webhook.secret')),
                'X-Webhook-Id' => $this->payload['id'],
            ])
                ->withBody($body, 'application/json')
                ->timeout(10)
                ->post($url)
                ->throw();
        } catch (\Throwable $e) {
            // Queued: rethrow so the backoff ladder retries. Sync (no queue
            // infrastructure): park the lead locally, never lose it.
            if ($this->job !== null && $this->job->getConnectionName() !== 'sync') {
                throw $e;
            }

            Storage::append('leads.jsonl', $body);
            Log::warning('Lead forward failed on sync queue; appended to storage leads.jsonl', [
                'lead' => $this->payload['id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        // The job (payload included) is in failed_jobs for queue:retry; log
        // the lead too so it is greppable even if that table is pruned.
        Log::error('Lead forwarding exhausted retries', [
            'payload' => $this->payload,
            'error' => $e->getMessage(),
        ]);
    }
}
