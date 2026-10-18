<?php

use App\Services\Kami\KamiApi;
use App\Services\Kami\KamiApiException;
use App\Services\Kami\VisitorSession;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config([
        'kami.admin_url' => 'https://admin.test/',
        'kami.site_token' => 'site-secret',
        'kami.visitor_cookie' => 'kami_vid',
        'kami.login_path' => '/login',
    ]);
});

function adminError(int $status, string $code, array $extra = []): array
{
    return ['error' => ['code' => $code, 'message' => "Admin said {$code}."] + $extra];
}

it('calls the network api with the site token', function () {
    Http::fake(['admin.test/api/network/v1/channels*' => Http::response(['data' => []])]);

    app(KamiApi::class)->network()->get('/channels', ['page' => 2]);

    Http::assertSent(fn(ClientRequest $r) => $r->url() === 'https://admin.test/api/network/v1/channels?page=2'
        && $r->header('X-Site-Token') === ['site-secret']
        && $r->header('Accept') === ['application/json']
        && !$r->hasHeader('Authorization'));
});

it('forwards the visitor account token and visitor id', function () {
    Http::fake(['admin.test/*' => Http::response(['data' => ['id' => 7]])]);
    Route::middleware('web')->get('/_kami/me', fn(KamiApi $api) => $api->visitor()->get('/me')->json('data'));

    $this->withSession(['kami.token' => 'acct-token'])
        ->withCookie('kami_vid', 'browser-1')
        ->get('/_kami/me')
        ->assertOk()
        ->assertJson(['id' => 7]);

    Http::assertSent(fn(ClientRequest $r) => $r->header('Authorization') === ['Bearer acct-token']
        && $r->header('X-Visitor-Id') === ['browser-1']);
});

it('turns admin errors into an exception carrying code, fields and retry time', function () {
    Http::fake([
        'admin.test/api/network/v1/auth/register' => Http::response(adminError(422, 'validation_failed', ['fields' => ['email' => ['Taken.']]]), 422),
        'admin.test/api/network/v1/auth/login' => Http::response(adminError(429, 'too_many_attempts', ['retry_after' => 42]), 429),
    ]);
    $api = app(KamiApi::class);

    $e = rescue(fn() => $api->network()->post('/auth/register'), fn($e) => $e, false);
    expect($e)->toBeInstanceOf(KamiApiException::class)
        ->and($e->status)->toBe(422)
        ->and($e->is('validation_failed'))->toBeTrue()
        ->and($e->fields)->toBe(['email' => ['Taken.']]);

    $e = rescue(fn() => $api->network()->post('/auth/login'), fn($e) => $e, false);
    expect($e->errorCode)->toBe('too_many_attempts')->and($e->retryAfter)->toBe(42);
});

it('logs the visitor out and sends them to login when their token is dead', function () {
    Http::fake(['admin.test/*' => Http::response(adminError(401, 'unauthenticated'), 401)]);
    Route::middleware('web')->get('/_kami/wallet', fn(KamiApi $api) => $api->visitor()->get('/me/wallet')->json());

    $this->withSession(['kami.token' => 'dead', 'kami.account' => ['id' => 1], 'cart' => [3]])
        ->get('/_kami/wallet')
        ->assertRedirect('/login')
        ->assertSessionMissing('kami.token')
        ->assertSessionHas('cart', [3]);
});

it('sends validation errors back to the form', function () {
    Http::fake(['admin.test/*' => Http::response(adminError(422, 'validation_failed', ['fields' => ['password' => ['Too short.']]]), 422)]);
    Route::middleware('web')->post('/_kami/register', fn(KamiApi $api) => $api->network()->post('/auth/register', request()->all()));

    $this->from('/register')
        ->post('/_kami/register', ['email' => 'a@b.test', 'password' => 'x'])
        ->assertRedirect('/register')
        ->assertSessionHasErrors(['password' => 'Too short.'])
        ->assertSessionHasInput('email', 'a@b.test');
});

it('answers json requests with the admin error and status', function () {
    Http::fake(['admin.test/*' => Http::response(adminError(403, 'blocked'), 403)]);
    Route::middleware('web')->post('/_kami/tip', fn(KamiApi $api) => $api->visitor()->post('/channels/x/tips'));

    $this->postJson('/_kami/tip')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'blocked');
});

it('shows the matching error page for failed page loads', function () {
    Http::fake([
        'admin.test/api/network/v1/posts/*' => Http::response(adminError(404, 'not_found'), 404),
        'admin.test/api/network/v1/feed' => Http::response(adminError(503, 'site_not_live'), 503),
    ]);
    Route::middleware('web')->get('/_kami/post', fn(KamiApi $api) => $api->network()->get('/posts/9'));
    Route::middleware('web')->get('/_kami/feed', fn(KamiApi $api) => $api->network()->get('/feed'));

    $this->get('/_kami/post')->assertNotFound();
    $this->get('/_kami/feed')->assertStatus(503);
});

it('stores and forgets the login in the session only', function () {
    $session = app(VisitorSession::class);
    expect($session->check())->toBeFalse();

    $session->start(['token' => 'tok', 'account' => ['id' => 5, 'username' => 'jane']]);
    expect($session->check())->toBeTrue()
        ->and($session->token())->toBe('tok')
        ->and($session->account())->toBe(['id' => 5, 'username' => 'jane']);

    $session->forget();
    expect($session->check())->toBeFalse()->and($session->account())->toBeNull();
});

it('gives each browser a visitor id cookie once the client is configured', function () {
    Route::middleware('web')->get('/_kami/page', fn() => request()->cookie('kami_vid'));

    $response = $this->get('/_kami/page')->assertCookie('kami_vid');
    expect($response->getContent())->toMatch('/^[0-9a-f-]{36}$/');
});

it('keeps an existing visitor id', function () {
    Route::middleware('web')->get('/_kami/page', fn() => request()->cookie('kami_vid'));

    $this->withCookie('kami_vid', 'kept')->get('/_kami/page')->assertCookieMissing('kami_vid')->assertSee('kept');
});

it('stays inert without admin configuration', function () {
    config(['kami.site_token' => null]);
    Route::middleware('web')->get('/_kami/page', fn() => 'ok');

    $this->get('/_kami/page')->assertCookieMissing('kami_vid');
    expect(fn() => app(KamiApi::class)->network())->toThrow(RuntimeException::class, 'KAMI_SITE_TOKEN');
});
