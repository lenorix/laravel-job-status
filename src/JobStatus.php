<?php

namespace Lenorix\LaravelJobStatus;

use Illuminate\Contracts\Queue\Job;
use Lenorix\LaravelJobStatus\Models\JobTracker;

class JobStatus {
    public static function of(object|string $job): ?JobTracker
    {
        if (property_exists($job, 'tracker') && $job->tracker instanceof JobTracker) {
            return $job->tracker;
        }

        return null;
    }
}
