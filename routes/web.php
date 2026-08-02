<?php

use Illuminate\Support\Facades\Route;

// ======================================================
// Static Sites (domain-scoped — must be registered first
// so they take precedence over the host-agnostic routes)
// ======================================================

require __DIR__ . '/sites.php';

// The health check (/up) is registered via withRouting(health:) in
// bootstrap/app.php — no need to re-register it here.

// ======================================================
// Main Routes (English - default, no prefix)
// ======================================================

Route::middleware('detect.locale')->group(function () {
    Route::get('/', fn() => view('pages.home'))->name('home');

    // Add your English routes here:
    // Route::get('/about', [PageController::class, 'about'])->name('about');
    // Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    // Route::get('/items/{slug}', [ItemController::class, 'show'])->name('items.show');
    // Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
});

// ======================================================
// Localized Routes (/{locale}/...)
// ======================================================
// The default locale stays unprefixed; every other supported locale gets
// a /{locale} prefix. The group's name prefix turns e.g. "home" into
// "localized.home" — generate URLs with localized_route('home').

$supportedLocales = array_keys(config('locales.supported', []));
$localePattern = implode('|', array_filter($supportedLocales, fn($l) => $l !== 'en'));

// Single-locale apps get no prefixed routes at all. The guard is required:
// an empty where-pattern makes route compilation throw ("requirement cannot
// be empty"), which also breaks route:cache.
if ($localePattern !== '') {
    Route::prefix('{locale}')
        ->where(['locale' => $localePattern])
        ->middleware('set.locale')
        ->name('localized.')
        ->group(function () {
            Route::get('/', fn() => view('pages.home'))->name('home');

            // Mirror the English routes here with the same names — the
            // group adds the "localized." prefix automatically:
            // Route::get('/about', [PageController::class, 'about'])->name('about');
            // Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        });
}

// ======================================================
// Legal / Static Pages (no locale prefix needed)
// ======================================================

// Route::get('/about', fn() => view('legal.about'))->name('about');
// Route::get('/privacy', fn() => view('legal.privacy'))->name('privacy');
// Route::get('/terms', fn() => view('legal.terms'))->name('terms');

// ======================================================
// Demo / design-development routes (local environment only —
// never registered in production, never in the route cache)
// ======================================================

if (app()->environment('local')) {
    require __DIR__ . '/dev.php';
}
