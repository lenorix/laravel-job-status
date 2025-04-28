<?php

namespace Lenorix\LaravelJobStatus\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Lenorix\LaravelJobStatus\Enums\JobStep;

class JobTracker extends Model
{
    use HasUlids;

    protected $fillable = [
        'status',
        'attempts',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function isSuccessful(): bool
    {
        return $this->status === JobStep::PROCESSED;
    }

    public function isFailed(): bool
    {
        return $this->status === JobStep::FAILED;
    }

    public function isPending(): bool
    {
        return !$this->isSuccessful() && !$this->isFailed();
    }
}
