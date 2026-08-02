<?php

use App\Services\SeoManager;

it('renders the home page meta from the pages map', function () {
    // pages/home.blade.php calls seo()->page('home'); the values come from
    // config/seo.php → 'pages'.
    $this->get('/')
        ->assertOk()
        ->assertSee('<title>Hello World', false)
        ->assertSee('<meta name="description" content="Your new project starts here.">', false);
});

it('interpolates :tokens from the data array', function () {
    config()->set('seo.pages.product', [
        'title' => ':name — specs, price and reviews',
        'description' => 'Everything about :name. Free shipping.',
    ]);

    $seo = app(SeoManager::class);
    $seo->page('product', ['name' => 'Tent']);

    expect($seo->title())->toContain('Tent — specs, price and reviews')
        ->and($seo->get('description'))->toBe('Everything about Tent. Free shipping.');
});

it('lets explicit seo() calls win over the pages map', function () {
    config()->set('seo.pages.product', ['title' => 'From config']);

    $seo = app(SeoManager::class);
    $seo->page('product');
    $seo->title('Custom title');

    expect($seo->title())->toContain('Custom title');
});

it('ignores unknown page keys', function () {
    $seo = app(SeoManager::class);
    $before = $seo->get('description');

    $seo->page('does-not-exist');

    expect($seo->get('description'))->toBe($before);
});
