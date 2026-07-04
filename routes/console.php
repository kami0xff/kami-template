<?php

use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\Telescope;

// Telescope stores a row per request/query/job/log entry, so prune aggressively
// to keep the table small. Guarded so a template without Telescope installed
// (or with it removed) still schedules cleanly.
if (class_exists(Telescope::class)) {
    Schedule::command('telescope:prune --hours=48')->daily();
}
