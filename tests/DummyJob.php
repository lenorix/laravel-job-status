<?php

namespace Lenorix\LaravelJobStatus\Tests;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Lenorix\LaravelJobStatus\Traits\Trackable;

class DummyJob implements ShouldQueue
{
    use Queueable;
    use Trackable;

    public function handle()
    {
        $this->setProgress(0.50);
        $this->setResult($this->input * 2);
    }

    public function __construct(
        public int $input,
    ) {
        $this->track();
        // To let emulate the job processing without underlying queue job
        $this->job = new class
        {
            public int $mockedAttempts = 1;

            public function attempts()
            {
                return $this->mockedAttempts;
            }
        };
    }

    // To let emulate the job processing
    public function payload(): array
    {
        return [];
    }
}
