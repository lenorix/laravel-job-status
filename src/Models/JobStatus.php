<?php

namespace Lenorix\LaravelJobStatus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

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
