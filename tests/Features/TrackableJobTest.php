<?php

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobTracker;
use Lenorix\LaravelJobStatus\Tests\DummyJob;

it('can track a job', function () {
    $job = DummyJob::trackDispatch(2)
        ->onConnection('sync')
        ->getJob();
    $tackerId = $job->tracker->id;

    expect($tackerId)->not->toBeNull();

    $tracker = JobTracker::find($tackerId);

    expect($tracker)->not->toBeNull()
        ->and($tracker->status)->toBe(JobStep::DISPATCHING->value)
        ->and($tracker->attempts)->toBe(0)
        ->and($tracker->result)->toBeNull();

    // Simulate the job processing with one fail to test event listeners

    Event::dispatch(new JobProcessing('sync', $job));
    $tracker->refresh();

    expect($tracker->status)->toBe(JobStep::PROCESSING->value)
        ->and($tracker->attempts)->toBe(1)
        ->and($tracker->result)->toBeNull();

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
