<?php

namespace Lenorix\LaravelJobStatus\Tests;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Lenorix\LaravelJobStatus\Traits\Trackable;

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
