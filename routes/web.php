<?php

use Illuminate\Support\Facades\Route;

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
