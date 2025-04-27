<?php

namespace Lenorix\LaravelJobStatus;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Lenorix\LaravelJobStatus\Commands\LaravelJobStatusCommand;

class LaravelJobStatusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-job-status')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_job_status_table')
            ->hasCommand(LaravelJobStatusCommand::class);
    }
}
