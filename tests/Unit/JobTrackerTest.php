<?php

use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobTracker;

it('check tracker status methods', function () {
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

it('prune tracker', function () {
    for ($i = 0; $i < 10; $i++) {
        JobTracker::create();
    }
    expect(JobTracker::count())->toBeGreaterThanOrEqual(10);

    config(['job-status.prune_days' => 0]);

    (new JobTracker)->pruneAll();
    expect(JobTracker::count())->toBe(0);
});

it('handle 0.00% progress', function () {
    $trackerId = JobTracker::create([
        'progress' => 0.00,
    ])->id;
    $tracker = JobTracker::find($trackerId);
    $tracker->refresh();

    expect($tracker->progress)->toBe(0.00);
});

it('handle 1.00% progress', function () {
    $trackerId = JobTracker::create([
        'progress' => 1.00,
    ])->id;
    $tracker = JobTracker::find($trackerId);
    $tracker->refresh();

    expect($tracker->progress)->toBe(1.00);
});
