<?php

namespace App\DTOs;

/**
 * Reference DTO — the shape validated data takes when it crosses a layer
 * boundary. Controllers validate, build one of these, and hand it to an
 * Action; Actions and Queries never touch the request.
 *
 * Immutable by construction (readonly + promoted properties), so anything
 * holding one can trust it hasn't been mutated along the way.
 *
 * Scaffold your own with: php artisan make:dto OrderData
 */
final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    /**
     * Build from validated input, e.g. UserData::fromArray($request->validated()).
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
        );
    }
}
