<?php

namespace Lenorix\LaravelJobStatus;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Events\JobQueueing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Facades\JobStatus;
use Lenorix\LaravelJobStatus\Models\JobTracker;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class JobStatusServiceProvider extends PackageServiceProvider
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
            ->hasMigration('create_laravel_job_status_table');
    }

    public function bootingPackage()
    {
        parent::bootingPackage();

        Event::listen(function (JobQueueing $event) {
            $tracker = JobStatus::of($event->job);
            if ($tracker) {
                JobTracker::where('id', $tracker->id)
                    ->whereIn('status', [
                        JobStep::DISPATCHING,
                        JobStep::FAILED,
                    ])
                    ->update([
                        'status' => JobStep::QUEUING,
                        'updated_at' => now(),
                    ]);
                $tracker->refresh();
            }
        });

        Event::listen(function (JobQueued $event) {
            $tracker = JobStatus::of($event->job);
            if ($tracker) {
                JobTracker::where('id', $tracker->id)
                    ->where('status', JobStep::QUEUING)
                    ->update([
                        'status' => JobStep::QUEUED,
                        'updated_at' => now(),
                    ]);
                $tracker->refresh();
            }
        });

        Queue::before(function (JobProcessing $event) {
            $tracker = JobStatus::of($event->job);
            if ($tracker) {
                JobTracker::where('id', $tracker->id)
                    ->whereIn('status', [
                        JobStep::DISPATCHING,
                        JobStep::QUEUING,
                        JobStep::QUEUED,
                        JobStep::FAILED,
                    ])
                    ->update([
                        'status' => JobStep::PROCESSING,
                        'attempts' => $event->job->attempts(),
                        'updated_at' => now(),
                    ]);
                $tracker->refresh();
            }
        });

        Queue::after(function (JobProcessed $event) {
            $tracker = JobStatus::of($event->job);
            if ($tracker) {
                JobTracker::where('id', $tracker->id)
                    ->update([
                        'status' => JobStep::PROCESSED,
                        'attempts' => $event->job->attempts(),
                        'updated_at' => now(),
                    ]);
                $tracker->refresh();
            }
        });

        Queue::failing(function (JobFailed $event) {
            $tracker = JobStatus::of($event->job);
            if ($tracker) {
                JobTracker::where('id', $tracker->id)
                    ->update([
                        'status' => JobStep::FAILED,
                        'attempts' => $event->job->attempts(),
                        'updated_at' => now(),
                    ]);
                $tracker->refresh();
            }
        });
    }
}
