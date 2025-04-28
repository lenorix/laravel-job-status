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
    JobTracker::create();
    expect(JobTracker::count())->toBe(1);

    config(['job-status.prune_days' => 0]);

    (new JobTracker)->pruneAll();
    expect(JobTracker::count())->toBe(0);
});
