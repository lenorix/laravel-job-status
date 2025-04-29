<?php

// config for Lenorix/LaravelJobStatus
return [
    /**
     * Days to keep job trackers in the database before to be prunable.
     */
    'prune_days' => env('JOB_STATUS_PRUNE_DAYS', 30),
];
