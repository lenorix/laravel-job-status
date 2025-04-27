<?php

namespace Lenorix\LaravelJobStatus\Enums;

enum JobStatus: string
{
    case DISPATCHING = 'dispatching';
    case QUEUING = 'queuing';
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case FAILED = 'failed';

    public static function default(): self
    {
        return self::DISPATCHING;
    }
}
