<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;

afterEach(function () {
    File::delete(resource_path('sites/example/clusters/garden-sheds.yaml'));
    File::delete(resource_path('sites/example/content/blog/garden-shed-cost.md'));
});

it('renders the pillar link box on spoke articles', function () {
    $this->get('http://example.test/blog/hello-world')
        ->assertOk()
        ->assertSee('Part of our guide:')
        ->assertSee('href="https://example.test/blog/writing-content"', false);
});

it('renders the spoke list on the pillar article', function () {
    $this->get('http://example.test/blog/writing-content')
        ->assertOk()
        ->assertSee('In this guide')
        ->assertSee('href="https://example.test/blog/hello-world"', false);
});

it('plans a topic cluster and saves an editable yaml', function () {
    config(['services.anthropic.api_key' => 'test-key']);

    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [['text' => json_encode([
                'topic' => 'Garden sheds',
                'keyword' => 'garden shed',
                'pillar' => [
                    'slug' => 'garden-shed-guide',
                    'title' => 'Garden Shed Guide',
                    'keyword' => 'garden shed',
                    'angle' => 'The definitive guide.',
                ],
                'spokes' => [
                    [
                        'slug' => 'garden-shed-cost',
                        'title' => 'How Much Does a Garden Shed Cost?',
                        'keyword' => 'garden shed cost',
                        'intent' => 'commercial',
                        'angle' => 'Price breakdown by size and material.',
                    ],
                ],
            ])]],
        ]),
    ]);

    $this->artisan('site:cluster', ['site' => 'example', 'topic' => 'garden sheds'])
        ->assertSuccessful()
        ->expectsOutputToContain('garden-sheds.yaml');

    $plan = Yaml::parseFile(
        resource_path('sites/example/clusters/garden-sheds.yaml')
    );

    expect($plan['pillar']['slug'])->toBe('garden-shed-guide')
        ->and($plan['pillar']['status'])->toBe('planned')
        ->and($plan['spokes'][0]['status'])->toBe('planned');
});

it('drafts planned spokes via site:write and tracks progress', function () {
    config(['services.anthropic.api_key' => 'test-key']);

    File::put(resource_path('sites/example/clusters/garden-sheds.yaml'), <<<'YAML'
    topic: 'Garden sheds'
    keyword: 'garden shed'
    pillar:
      slug: garden-shed-guide
      title: 'Garden Shed Guide'
      keyword: 'garden shed'
      status: planned
    spokes:
      - slug: garden-shed-cost
        title: 'How Much Does a Garden Shed Cost?'
        keyword: 'garden shed cost'
        angle: 'Price breakdown.'
        status: planned
    YAML);

    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [['text' => json_encode([
                'title' => 'How Much Does a Garden Shed Cost?',
                'description' => 'Real prices by size and material.',
                'markdown' => "# How much does a garden shed cost?\n\nSee our [garden shed guide](/blog/garden-shed-guide).",
            ])]],
        ]),
    ]);

    $this->artisan('site:cluster', [
        'site' => 'example',
        'topic' => 'garden-sheds',
        '--write' => 1,
    ])->assertSuccessful();

    $file = resource_path('sites/example/content/blog/garden-shed-cost.md');
    expect(File::exists($file))->toBeTrue()
        ->and(File::get($file))->toContain('cluster: garden-sheds');

    $plan = Yaml::parseFile(
        resource_path('sites/example/clusters/garden-sheds.yaml')
    );

    expect($plan['spokes'][0]['status'])->toBe('drafted')
        ->and($plan['pillar']['status'])->toBe('planned'); // budget spent on the spoke

    // The spoke prompt got the hub-and-spoke instructions.
    Http::assertSent(function ($request) {
        $prompt = $request->data()['messages'][0]['content'] ?? '';

        return str_contains($prompt, 'SPOKE in the "Garden sheds" topic cluster')
            && str_contains($prompt, '/blog/garden-shed-guide');
    });
});
