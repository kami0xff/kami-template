<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Requires `shared_preload_libraries=pg_stat_statements` on the server
        // (set in docker-compose). Without it the table exists but querying it
        // errors, so this extension is only meaningful on Postgres.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_stat_statements');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP EXTENSION IF EXISTS pg_stat_statements');
        }
    }
};
