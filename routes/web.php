<?php

use Illuminate\Support\Facades\Route;

// ======================================================
// Static Sites (domain-scoped — must be registered first
// so they take precedence over the host-agnostic routes)
// ======================================================

require __DIR__ . '/sites.php';

// ======================================================
// Health Check
// ======================================================

Route::get('/up', fn() => response('OK', 200));

// ======================================================
// Main Routes (English - default, no prefix)
// ======================================================

Route::middleware('detect.locale')->group(function () {
    Route::get('/', fn() => view('pages.seo-showcase'))->name('home');
    Route::get('/seo-showcase', fn() => view('pages.seo-showcase'))->name('seo-showcase');
    Route::get('/design', fn() => view('pages.design'))->name('design');
    Route::get('/shader-hero', fn() => view('pages.shader-hero'))->name('shader-hero');
    Route::get('/payment', fn() => view('pages.payment'))->name('payment');
    Route::get('/ai', fn() => view('pages.ai'))->name('ai');
    Route::get('/hhentai', fn() => view('pages.hhentai'))->name('hhentai');
    Route::get('/pornguru-theme', fn() => view('pages.pornguru-theme'))->name('pornguru-theme');
    Route::get('/caumont', fn() => view('caumont.pages.home'))->name('caumont');
    Route::get('/caumont/villa-esterel', fn() => view('caumont.pages.villa-esterel'))->name('caumont.villa');

    // Add your English routes here:
    // Route::get('/about', [PageController::class, 'about'])->name('about');
    // Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    // Route::get('/items/{slug}', [ItemController::class, 'show'])->name('items.show');
    // Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
});

// ======================================================
// Localized Routes (/{locale}/...)
// ======================================================

$supportedLocales = array_keys(config('locales.supported', []));
$localePattern = implode('|', array_filter($supportedLocales, fn($l) => $l !== 'en'));

Route::prefix('{locale}')
    ->where(['locale' => $localePattern])
    ->middleware('set.locale')
    ->group(function () {
        Route::get('/', fn() => view('pages.seo-showcase'))->name('home.localized');
        Route::get('/seo-showcase', fn() => view('pages.seo-showcase'))->name('seo-showcase.localized');

        // Mirror all English routes here with ".localized" suffix:
        // Route::get('/about', [PageController::class, 'about'])->name('about.localized');
        // Route::get('/items', [ItemController::class, 'index'])->name('items.index.localized');
    });

// ======================================================
// Legal / Static Pages (no locale prefix needed)
// ======================================================

// Route::get('/about', fn() => view('legal.about'))->name('about');
// Route::get('/privacy', fn() => view('legal.privacy'))->name('privacy');
// Route::get('/terms', fn() => view('legal.terms'))->name('terms');
