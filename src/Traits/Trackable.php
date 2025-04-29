<?php

namespace Lenorix\LaravelJobStatus\Traits;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\Attributes\WithoutRelations;
use Lenorix\LaravelJobStatus\Enums\JobStep;
use Lenorix\LaravelJobStatus\Models\JobTracker;

/**
 * Track a job and allow to get a result in the future.
 *
 * After `use` this trait, call `$this->track()` in the job constructor,
 *  or use `trackDispatch()` static method to dispatch and track.
 *
 * The tracker is in `$job->tracker` property, has an `id` and
 *  can be used with `JobTracker::find($id)` to get the job status
 *  and result.
 */
trait Trackable
{
    use Dispatchable;

    /**
     * Use it only to read, not modify it to ensure the track
     *  of the job works correctly.
     */
    #[WithoutRelations]
    public ?JobTracker $tracker = null;

    /**
     * Don't use it directly from the job, use `setResult()`
     *  to set the result instead.
     *
     * It's used to track a job after calling `dispatch()`
     *  like:
     *
     * ```php
     * $tracker = DummyJob::dispatch(2)
     *     ->getJob()
     *     ->tracker();
     * ```
     */
    public function tracker(): JobTracker
    {
        $this->track();

        return $this->tracker;
    }

    /**
     * Dispatch and track at the same time.
     */
    public static function dispatchWithTrack(mixed ...$arguments): PendingDispatch
    {
        $dispatch = static::dispatch(...$arguments);

        $dispatch->getJob()->track();

        return $dispatch;
    }

    /**
     * Idempotently track the job.
     */
    protected function track(): void
    {
        if (is_null($this->tracker)) {
            $this->tracker = JobTracker::create([
                'status' => JobStep::DISPATCHING,
                'attempts' => 0,
            ]);
        }
    }

    /**
     * Use it from a job to set result in the tracker.
     */
    protected function setResult(mixed $result): void
    {
        if (! is_null($this->tracker)) {
            JobTracker::where('id', $this->tracker->id)
                ->update([
                    'result' => $result,
                    'updated_at' => now(),
                ]);
            $this->tracker->refresh();
        }
    }

    /**
     * Use it from a job to set progress in the tracker.
     *
     * @throws \InvalidArgumentException if the progress is not between 0.0 and 100.0
     */
    protected function setProgress(float $progress): void
    {
        if ($progress < 0.00 || $progress > 100.00) {
            throw new \InvalidArgumentException('Progress must be between 0.0 and 100.0');
        }

        if (! is_null($this->tracker)) {
            JobTracker::where('id', $this->tracker->id)
                ->update([
                    'progress' => $progress,
                    'updated_at' => now(),
                ]);
            $this->tracker->refresh();
        }
    }

    /**
     * You only need to call this, from job handle method,
     *  if your job late more than configured prune days
     *  to finish. Default prune days is 30 days.
     */
    protected function touchTracker(): void
    {
        $this->tracker?->touch();
    }
}
