<?php

namespace App\Services\Kami;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * A non-2xx answer from the admin: {"error": {"code", "message", ...}}.
 * Branch on `$errorCode`, never on the message (wording may change).
 *
 * Uncaught, it renders as:
 *  - 401 unauthenticated  → login forgotten, visitor sent to kami.login_path
 *  - 422 validation_failed → back to the form with the admin's field errors
 *  - 429 too_many_attempts → back with a "try again in N seconds" error
 *  - 503 site_not_live    → the maintenance page
 *  - other 4xx            → GET: that status's error page; forms: back with the message
 *  - 5xx                  → the 500 page (and reported)
 * JSON requests get the admin's error body with the same status.
 */
final class KamiApiException extends RuntimeException
{
    /**
     * @param  array<string, list<string>>  $fields  validation errors keyed like the request fields
     * @param  array<string, mixed>  $error  the full `error` object (extra keys such as `types`)
     */
    public function __construct(
        public readonly int $status,
        public readonly string $errorCode,
        string $message,
        public readonly array $fields = [],
        public readonly ?int $retryAfter = null,
        public readonly array $error = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public static function fromResponse(Response $response, ?RequestException $previous = null): self
    {
        $error = $response->json('error');
        $error = is_array($error) ? $error : [];

        return new self(
            status: $response->status(),
            errorCode: (string) ($error['code'] ?? ($response->serverError() ? 'server_error' : 'http_' . $response->status())),
            message: (string) ($error['message'] ?? 'The admin API answered ' . $response->status() . '.'),
            fields: is_array($error['fields'] ?? null) ? $error['fields'] : [],
            retryAfter: isset($error['retry_after']) ? (int) $error['retry_after'] : null,
            error: $error,
            previous: $previous,
        );
    }

    public function is(string $code): bool
    {
        return $this->errorCode === $code;
    }

    /** Client errors are answers, not bugs; only the admin failing gets reported. */
    public function report(): bool
    {
        return $this->status < 500;
    }

    public function render(Request $request): HttpResponse
    {
        if ($this->is('unauthenticated')) {
            app(VisitorSession::class)->forget();
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => ['code' => $this->errorCode, 'message' => $this->getMessage()] + $this->error], $this->status);
        }

        return match (true) {
            $this->is('unauthenticated') => redirect()->guest((string) config('kami.login_path')),
            $this->is('validation_failed') => back()->withErrors($this->fields)->withInput(),
            $this->is('too_many_attempts') => back()->withErrors(['kami' => __('Too many attempts, try again in :seconds seconds.', ['seconds' => $this->retryAfter ?? 60])])->withInput(),
            $this->is('site_not_live') => $this->page($request, 503),
            $this->status < 500 && !$request->isMethodSafe() => back()->withErrors(['kami' => $this->getMessage()])->withInput(),
            default => $this->page($request, $this->status < 500 ? $this->status : 500),
        };
    }

    private function page(Request $request, int $status): HttpResponse
    {
        return app(ExceptionHandler::class)->render($request, new HttpException($status, $this->getMessage(), $this));
    }
}
