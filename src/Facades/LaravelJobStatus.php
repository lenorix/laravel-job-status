<?php

namespace Lenorix\LaravelJobStatus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lenorix\LaravelJobStatus\LaravelJobStatus
 */
class LaravelJobStatus extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Lenorix\LaravelJobStatus\LaravelJobStatus::class;
    }
}
