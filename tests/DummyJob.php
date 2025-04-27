<?php

namespace Lenorix\LaravelJobStatus\Tests;

use Lenorix\LaravelJobStatus\Traits\Trackable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DummyJob implements ShouldQueue
{
    use Queueable;
    use Trackable;

    public function __construct(
        public int $input,
    ) {}

    public function handle()
    {
        $this->setResult($this->input * 2);
    }
}
