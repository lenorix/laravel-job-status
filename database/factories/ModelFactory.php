<?php

namespace Lenorix\LaravelJobStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lenorix\LaravelJobStatus\Models\JobTracker;

class ModelFactory extends Factory
{
    protected $model = JobTracker::class;

    public function definition()
    {
        return [
        ];
    }
}
