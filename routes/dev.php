<?php

use Illuminate\Support\Facades\Route;

// ======================================================
// Demo / design-development routes (local environment only)
// ======================================================
// Loaded from web.php via app()->environment('local'). These pages exist
// for UI/UX development of the template itself — projects created from
// the template can delete this file along with resources/views/pages/*.

Route::middleware('detect.locale')->group(function () {
    Route::get('/seo-showcase', fn() => view('pages.seo-showcase'))->name('seo-showcase');
    Route::get('/design', fn() => view('pages.design'))->name('design');
    Route::get('/shader-hero', fn() => view('pages.shader-hero'))->name('shader-hero');
    Route::get('/payment', fn() => view('pages.payment'))->name('payment');
    Route::get('/ai', fn() => view('pages.ai'))->name('ai');
    Route::get('/hhentai', fn() => view('pages.hhentai'))->name('hhentai');
    Route::get('/pornguru-theme', fn() => view('pages.pornguru-theme'))->name('pornguru-theme');
    Route::get('/caumont', fn() => view('caumont.pages.home'))->name('caumont');
    Route::get('/caumont/villa-esterel', fn() => view('caumont.pages.villa-esterel'))->name('caumont.villa');
});

// Localized mirrors of the demo pages (same "localized." name prefix as
// web.php). Self-contained on purpose: computes its own locale pattern so
// this file keeps working no matter how web.php is edited.
$devLocalePattern = implode('|', array_filter(
    array_keys(config('locales.supported', [])),
    fn($l) => $l !== 'en'
));

if ($devLocalePattern !== '') {
    Route::prefix('{locale}')
        ->where(['locale' => $devLocalePattern])
        ->middleware('set.locale')
        ->name('localized.')
        ->group(function () {
            Route::get('/seo-showcase', fn() => view('pages.seo-showcase'))->name('seo-showcase');
        });
}
