<?php

use App\Actions\CreateUser;
use App\DTOs\UserData;
use App\Models\User;
use App\Queries\UserIndexQuery;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

it('creates a user through the dto and action chain', function () {
    $user = app(CreateUser::class)->handle(UserData::fromArray([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'secret-password',
    ]));

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->email)->toBe('ada@example.com')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});

it('reads users through a query class', function () {
    User::factory()->create(['name' => 'Grace Hopper', 'email' => 'grace@example.com']);
    User::factory()->create(['name' => 'Alan Turing', 'email' => 'alan@example.com']);

    $result = app(UserIndexQuery::class)->handle(search: 'grace');

    expect($result->total())->toBe(1)
        ->and($result->first()->name)->toBe('Grace Hopper');
});

it('scaffolds actions, queries, and dtos with the generators', function () {
    $generated = [
        'make:action' => app_path('Actions/Testing/ScaffoldedAction.php'),
        'make:query' => app_path('Queries/Testing/ScaffoldedQuery.php'),
        'make:dto' => app_path('DTOs/Testing/ScaffoldedData.php'),
    ];

    try {
        $this->artisan('make:action', ['name' => 'Testing/ScaffoldedAction'])->assertSuccessful();
        $this->artisan('make:query', ['name' => 'Testing/ScaffoldedQuery'])->assertSuccessful();
        $this->artisan('make:dto', ['name' => 'Testing/ScaffoldedData'])->assertSuccessful();

        foreach ($generated as $file) {
            expect($file)->toBeFile();
        }

        expect(file_get_contents($generated['make:action']))
            ->toContain('namespace App\Actions\Testing;')
            ->toContain('final class ScaffoldedAction');
        expect(file_get_contents($generated['make:dto']))
            ->toContain('final readonly class ScaffoldedData');
    } finally {
        File::deleteDirectory(app_path('Actions/Testing'));
        File::deleteDirectory(app_path('Queries/Testing'));
        File::deleteDirectory(app_path('DTOs/Testing'));
    }
});
