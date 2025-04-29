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
        ->and($tracker->status)->toBe(JobStep::DISPATCHING)
        ->and($tracker->attempts)->toBe(0)
        ->and($tracker->progress)->toBe(0.00)
        ->and($tracker->result)->toBeNull();

    // Simulate the job processing with one fail to test event listeners.

    Event::dispatch(new JobQueueing('sync', null, $job, 'null', null));
    Event::dispatch(new JobQueued('sync', null, null, $job, 'null', null));
    Event::dispatch(new JobProcessing('sync', $job));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSING)
        ->and($tracker->attempts)->toBe(1)
        ->and($tracker->progress)->toBe(0.00)
        ->and($tracker->result)->toBeNull();

    // Simulate race condition where the job starts processing faster than the event queued is fired.
    Event::dispatch(new JobQueued('sync', null, null, $job, 'null', null));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSING);

    Event::dispatch(new JobFailed('sync', $job, new Exception('Test exception')));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::FAILED)
        ->and($tracker->attempts)->toBe(1)
        ->and($tracker->progress)->not->toBe(1.00)
        ->and($tracker->result)->toBeNull();

    $job->job->mockedAttempts = 2;
    Event::dispatch(new JobProcessing('sync', $job));
    $job->handle();

    $tracker->refresh();
    expect($tracker->status)->toBe(JobStep::PROCESSING)
        ->and($tracker->attempts)->toBe(2)
        ->and($tracker->progress)->toBe(0.50)
        ->and($tracker->result)->not->toBeNull();

    Event::dispatch(new JobProcessed('sync', $job));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSED)
        ->and($tracker->attempts)->toBe(2)
        ->and($tracker->progress)->toBe(1.00)
        ->and($tracker->result)->toBe(4);
});

it('can track a job in a easy and juicy way', function () {
    $tracker = DummyJob::dispatch(4)
        ->onConnection('sync')
        ->afterResponse()
        ->getJob()
        ->tracker();

    expect($tracker)->not->toBeNull()
        ->and($tracker->status)->toBe(JobStep::DISPATCHING)
        ->and($tracker->attempts)->toBe(0)
        ->and($tracker->result)->toBeNull();
});
