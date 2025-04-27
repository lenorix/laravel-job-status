<?php

namespace Lenorix\LaravelJobStatus\Traits;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\Attributes\WithoutRelations;
use Lenorix\LaravelJobStatus\Models\JobStatus;

trait Trackable
{
    use Dispatchable;

    #[WithoutRelations]
    public ?JobStatus $jobStatus = null;

    public static function trackDispatch(mixed ...$arguments): PendingDispatch
    {
        $dispatch = static::dispatch(...$arguments);

        $jobStatus = \Lenorix\LaravelJobStatus\Models\JobStatus::create([
            'status' => \Lenorix\LaravelJobStatus\Enums\JobStatus::DISPATCHING,
        ]);
        $job = $dispatch->getJob();
        $job->jobStatus = $jobStatus;

        return $dispatch;
    }

    public function setResult(mixed $result): void
    {
        if ($this->jobStatus) {
            $this->jobStatus->result = $result;
            $this->jobStatus->save();
        }
    }
}
