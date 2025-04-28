<?php

namespace Lenorix\LaravelJobStatus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lenorix\LaravelJobStatus\JobStatus
 */
class JobStatus extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Lenorix\LaravelJobStatus\JobStatus::class;
    }
}
