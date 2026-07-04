<?php

use Illuminate\Support\Facades\Schedule;

// Telescope stores a row per request/query/job/log entry, so prune aggressively
// to keep the table small. Guarded so a template without Telescope installed
// (or with it removed) still schedules cleanly.
if (class_exists(\Laravel\Telescope\Telescope::class)) {
    Schedule::command('telescope:prune --hours=48')->daily();
}

