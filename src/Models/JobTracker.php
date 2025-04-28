<?php

namespace Lenorix\LaravelJobStatus\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Lenorix\LaravelJobStatus\Enums\JobStep;

/**
 * @property string $id Auto-generated ULID
 * @property JobStep $status
 * @property int $attempts
 * @property array $result
 */
class JobTracker extends Model
{
    use HasUlids;

    protected $fillable = [
        'status',
        'attempts',
        'result',
    ];

    protected $casts = [
        'status' => JobStep::class,
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
        return ! $this->isSuccessful() && ! $this->isFailed();
    }
}
