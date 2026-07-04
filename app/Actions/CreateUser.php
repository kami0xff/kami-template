<?php

namespace App\Actions;

use App\DTOs\UserData;
use App\Models\User;

/**
 * Reference Action — every write goes through exactly one of these. One
 * class per operation, one public handle() method, DTO in, model out.
 *
 * Because the action is the single write path, it is also the single place
 * for side effects (events, notifications, cache busting) and the natural
 * unit to call from anywhere: controllers, commands, jobs, or the admin
 * API layer.
 *
 * Scaffold your own with: php artisan make:action Orders/CreateOrder
 */
final class CreateUser
{
    public function handle(UserData $data): User
    {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password, // Hashed by the model's cast.
        ]);
    }
}
