<?php

namespace Lenorix\LaravelJobStatus\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

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
}
