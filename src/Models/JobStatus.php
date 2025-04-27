<?php

namespace Lenorix\LaravelJobStatus\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    use HasUlids;

    protected $fillable = [
        'status',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];
}
