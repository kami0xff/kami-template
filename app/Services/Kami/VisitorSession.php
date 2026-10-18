<?php

namespace App\Services\Kami;

/**
 * The visitor's network login, kept only in the server-side session: a
 * visitor is logged in iff the session holds an account token. The token
 * never goes to the browser.
 */
final class VisitorSession
{
    private const TOKEN = 'kami.token';

    private const ACCOUNT = 'kami.account';

    public function check(): bool
    {
        return $this->token() !== null;
    }

    public function token(): ?string
    {
        $token = session(self::TOKEN);

        return is_string($token) && $token !== '' ? $token : null;
    }

    /** @return array<string, mixed>|null the account as last returned by the admin */
    public function account(): ?array
    {
        $account = session(self::ACCOUNT);

        return is_array($account) ? $account : null;
    }

    /**
     * Store the `data` of a register/login answer ({token, account}); the
     * session id is rotated so a pre-login id can't be reused.
     *
     * @param  array{token: string, account: array<string, mixed>}  $auth
     */
    public function start(array $auth): void
    {
        session()->regenerate();
        session([self::TOKEN => $auth['token'], self::ACCOUNT => $auth['account']]);
    }

    /** @param  array<string, mixed>  $account  fresh `GET /me` data */
    public function refresh(array $account): void
    {
        session([self::ACCOUNT => $account]);
    }

    /** Drop the login but keep the rest of the session (cart, locale, …). */
    public function forget(): void
    {
        session()->forget([self::TOKEN, self::ACCOUNT]);
    }

    /** Full logout: the whole session goes. */
    public function end(): void
    {
        session()->invalidate();
        session()->regenerateToken();
    }
}
