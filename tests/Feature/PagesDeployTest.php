<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

afterEach(function () {
    File::deleteDirectory(public_path('static/example.test'));
});

it('emits a branded 404.html on every build', function () {
    $this->artisan('site:build', ['site' => 'example'])->assertSuccessful();

    $file = public_path('static/example.test/404.html');

    expect($file)->toBeFile()
        ->and(file_get_contents($file))->toContain('404')->toContain('Example Site');
});

it('emits the Pages lead worker only with --pages', function () {
    $this->artisan('site:build', ['site' => 'example'])->assertSuccessful();

    expect(public_path('static/example.test/_worker.js'))->not->toBeFile();

    $this->artisan('site:build', ['site' => 'example', '--pages' => true])->assertSuccessful();

    $worker = public_path('static/example.test/_worker.js');
    expect($worker)->toBeFile()
        ->and(file_get_contents($worker))
        ->toContain("const SITE_KEY = 'example';")
        ->toContain("const SITE_DOMAIN = 'example.test';")
        ->toContain('x-webhook-signature');

    $routes = json_decode((string) file_get_contents(public_path('static/example.test/_routes.json')), true);
    expect($routes)->toBe(['version' => 1, 'include' => ['/lead'], 'exclude' => []]);
});

it('ships a syntactically valid pages worker', function () {
    if (Process::run(['which', 'node'])->failed()) {
        $this->markTestSkipped('node not available');
    }

    // node --check parses ES modules only for .mjs files.
    $temp = sys_get_temp_dir() . '/pages-worker-check.mjs';
    File::copy(base_path('stubs/pages-worker.js'), $temp);

    try {
        $result = Process::run(['node', '--check', $temp]);
        expect($result->successful())->toBeTrue($result->errorOutput());
    } finally {
        File::delete($temp);
    }
});

it('refuses to deploy a missing site', function () {
    $this->artisan('site:deploy', ['site' => 'nope'])->assertFailed();
});
