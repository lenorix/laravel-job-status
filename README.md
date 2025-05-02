# Laravel Job Status

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lenorix/laravel-job-status.svg?style=flat-square)](https://packagist.org/packages/lenorix/laravel-job-status)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lenorix/laravel-job-status/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lenorix/laravel-job-status/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lenorix/laravel-job-status/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lenorix/laravel-job-status/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lenorix/laravel-job-status.svg?style=flat-square)](https://packagist.org/packages/lenorix/laravel-job-status)

Job status and result made easy and simple.

## Support us

Support [this work in GitHub](https://github.com/lenorix/laravel-job-status) or [get in contact](https://lenorix.com/).

## Requirements

- PHP 8.4 or 8.3
- Laravel 11.37 or higher (12 included)

Support for older versions is planned, if you can help with that, please open a PR.

## Installation

You can install the package via composer:

```bash
composer require lenorix/laravel-job-status
```

It requires Laravel 11 or higher, and is tested on 11.37 as lowest version supported.

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="job-status-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-job-status-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * Days to keep job trackers in the database before to be prunable.
     */
    'prune_days' => env('JOB_STATUS_PRUNE_DAYS', 30),
];
```

See [Laravel doc about pruning models](https://laravel.com/docs/11.x/eloquent#pruning-models) for details.

## Usage

In your job class:

```php
use Lenorix\LaravelJobStatus\Traits\Trackable;

class YourJob implements ShouldQueue
{
    use Queueable;
    use Trackable; // Add this trait to your job.

    public function handle()
    {
        // Optional, only if you want to get result with the tracker.
        // It must serialize to JSON well.
        $this->setResult(...);
    }
}
```

And when you dispatch the job:

```php
$tracker = YourJob::dispatchWithTrack(...)
        ->afterResponse()
        ...
        /// Get tracker to have a way to know when it's done.
        ->getJob()
        ->tracker();

$tracker->id; // Get the tracker ULID to check it in another request.

if ($tracker->isSuccessful()) {
    ...
}
if ($tracker->isFailed()) {
    ...
}
if ($tracker->isPending()) {
    ...
}

$tracker->result; // Get the result of the job, or null if not set.
```

If you have the ULID:

```php
use Lenorix\LaravelJobStatus\Facades\JobStatus;

$tracker = JobStatus::of($ulid);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jesus Hernandez](https://github.com/jhg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
