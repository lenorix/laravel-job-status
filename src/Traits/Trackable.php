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
            ]);
        }
    }

    /**
     * Let the job give a result by the tracker model.
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
     * You only need to call this from handle method
     *  if your job late more than **one month** to finish.
     *
     * The model is prunable, so it will be deleted
     *  if is not updated in one month. But it's not
     *  a common case to take so long to finish a job.
     */
    protected function touchTracker(): void
    {
        $this->tracker?->touch();
    }
}
