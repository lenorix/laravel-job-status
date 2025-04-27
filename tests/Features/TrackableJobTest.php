<?php

use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobStatus;
use Lenorix\LaravelJobStatus\Tests\DummyJob;

it('can track a job', function () {
    $job = DummyJob::trackDispatch(2);
    $id = $job->getJob()->jobStatus->id;

    expect($id)->not->toBeNull();

    $jobStatus = JobStatus::find($id);
    expect($jobStatus)->not->toBeNull();
    expect($jobStatus->status)->toBe(JobStep::DISPATCHING->value);
    expect($jobStatus->result)->toBeNull();

    $job->handle();
    $jobStatus->refresh();
    // expect($jobStatus->status)->toBe(JobStep::COMPLETED->value);
    expect($jobStatus->result)->toBe(4);
});
