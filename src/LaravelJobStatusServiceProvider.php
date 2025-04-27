<?php

namespace Lenorix\LaravelJobStatus;

use Lenorix\LaravelJobStatus\Commands\LaravelJobStatusCommand;
use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobTracker;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;

class LaravelJobStatusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-job-status')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_job_status_table')
            ->hasCommand(LaravelJobStatusCommand::class);
    }

    public function bootingPackage()
    {
        parent::bootingPackage();

        Queue::before(function (JobProcessing $event) {
            $job = $event->job;

            if (property_exists($job, 'tracker') && $job->tracker instanceof JobTracker) {
                $job->tracker->status = JobStep::PROCESSING;
                $job->tracker->attempts = $event->job->attempts();
                $job->tracker->touch();
                $job->tracker->save();
                $job->tracker->refresh();
            }
        });

        Queue::after(function (JobProcessed $event) {
            $job = $event->job;

            if (property_exists($job, 'tracker') && $job->tracker instanceof JobTracker) {
                $job->tracker->status = JobStep::PROCESSED;
                $job->tracker->attempts = $event->job->attempts();
                $job->tracker->touch();
                $job->tracker->save();
                $job->tracker->refresh();
            }
        });

        Queue::failing(function (JobFailed $event) {
            $job = $event->job;

            if (property_exists($job, 'tracker') && $job->tracker instanceof JobTracker) {
                $job->tracker->status = JobStep::FAILED;
                $job->tracker->attempts = $event->job->attempts();
                $job->tracker->touch();
                $job->tracker->save();
                $job->tracker->refresh();
            }
        });
    }
}
