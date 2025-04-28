<?php

namespace Lenorix\LaravelJobStatus\Enums;

enum JobStep: string
{
    case DISPATCHING = 'dispatching';
    case QUEUING = 'queuing';
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case PROCESSED = 'processed';

    public static function default(): self
    {
        return self::DISPATCHING;
    }
}
