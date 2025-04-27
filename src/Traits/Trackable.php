<?php

namespace Lenorix\LaravelJobStatus\Traits;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\Attributes\WithoutRelations;
use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobTracker;

trait Trackable
{
    use Dispatchable;

    #[WithoutRelations]
    public ?JobTracker $tracker = null;

    public static function trackDispatch(mixed ...$arguments): PendingDispatch
    {
        $dispatch = static::dispatch(...$arguments);

        $tracker = JobTracker::create([
            'status' => JobStep::DISPATCHING,
        ]);
        $job = $dispatch->getJob();
        $job->tracker = $tracker;

        return $dispatch;
    }

    protected function setResult(mixed $result): void
    {
        if ($this->tracker) {
            $this->tracker->result = $result;
            $this->tracker->save();
            $this->tracker->refresh();
        }
    }
}
