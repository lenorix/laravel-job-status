<?php

namespace Lenorix\LaravelJobStatus\Commands;

use Illuminate\Console\Command;

class LaravelJobStatusCommand extends Command
{
    public $signature = 'laravel-job-status';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
