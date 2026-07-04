<?php

namespace App\Queries;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Reference Query — every read goes through one of these. Queries are the
 * only layer that knows the schema (tables, columns, joins, or a read
 * replica / admin-exposed view), so a schema change breaks one folder,
 * not thirty controllers.
 *
 * Queries never write. If the project reads from a replica or a shared
 * admin database, point them at a read-only connection:
 *
 *     User::on('read')->query()
 *
 * Scaffold your own with: php artisan make:query Orders/OrderIndexQuery
 */
final class UserIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(?string $search = null, int $perPage = 25): LengthAwarePaginator
    {
        return User::query()
            ->when($search, fn(Builder $query) => $query
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('email', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }
}
