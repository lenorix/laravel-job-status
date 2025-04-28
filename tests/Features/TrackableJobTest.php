<?php

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Events\JobQueueing;
use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobTracker;
use Lenorix\LaravelJobStatus\Tests\DummyJob;

it('can track a job', function () {
    $job = DummyJob::dispatchWithTrack(2)
        ->onConnection('sync')
        ->getJob();
    $tackerId = $job->tracker->id;

    expect($tackerId)->not->toBeNull();

    $tracker = JobTracker::find($tackerId);

    expect($tracker)->not->toBeNull()
        ->and($tracker->status)->toBe(JobStep::DISPATCHING->value)
        ->and($tracker->attempts)->toBe(0)
        ->and($tracker->result)->toBeNull();

    // Simulate the job processing with one fail to test event listeners.

    Event::dispatch(new JobQueueing('sync', null, $job, 'null', null));
    Event::dispatch(new JobQueued('sync', null, null, $job, 'null', null));
    Event::dispatch(new JobProcessing('sync', $job));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSING->value)
        ->and($tracker->attempts)->toBe(1)
        ->and($tracker->result)->toBeNull();

    // Simulate race condition where the job starts processing faster than the event queued is fired.
    Event::dispatch(new JobQueued('sync', null, null, $job, 'null', null));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSING->value);

    Event::dispatch(new JobFailed('sync', $job, new Exception('Test exception')));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::FAILED->value)
        ->and($tracker->attempts)->toBe(1)
        ->and($tracker->result)->toBeNull();

    $job->job->mockedAttempts = 2;
    Event::dispatch(new JobProcessing('sync', $job));
    $job->handle();
    Event::dispatch(new JobProcessed('sync', $job));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSED->value)
        ->and($tracker->attempts)->toBe(2)
        ->and($tracker->result)->toBe(4);
});

it('uses tracker status methods', function () {
    $tracker = JobTracker::create();

    foreach (JobStep::cases() as $step) {
        $tracker->status = $step;

        switch ($step) {
            case JobStep::FAILED:
                expect($tracker->isSuccessful())->toBeFalse()
                    ->and($tracker->isFailed())->toBeTrue()
                    ->and($tracker->isPending())->toBeFalse();
                break;
            case JobStep::PROCESSED:
                expect($tracker->isSuccessful())->toBeTrue()
                    ->and($tracker->isFailed())->toBeFalse()
                    ->and($tracker->isPending())->toBeFalse();
                break;
            default:
                expect($tracker->isSuccessful())->toBeFalse()
                    ->and($tracker->isFailed())->toBeFalse()
                    ->and($tracker->isPending())->toBeTrue();
                break;
        }
    }
});
