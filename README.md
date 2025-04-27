# Job ULID, status and result made easy and simple

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lenorix/laravel-job-status.svg?style=flat-square)](https://packagist.org/packages/lenorix/laravel-job-status)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lenorix/laravel-job-status/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lenorix/laravel-job-status/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lenorix/laravel-job-status/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lenorix/laravel-job-status/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lenorix/laravel-job-status.svg?style=flat-square)](https://packagist.org/packages/lenorix/laravel-job-status)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-job-status.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-job-status)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require lenorix/laravel-job-status
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-job-status-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-job-status-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
class YourJob implements ShouldQueue
{
    use Queueable;
    use Trackable; // Add this trait to your job.

    public function __construct(
        public int $input,
    )
    {
        // Simplest way, alternatively `::trackDispatch()` call this for you.
        $this->track();
    }

    public function handle()
    {
        // Optional, only if you want to get result with the tracker.
        $this->setResult(...);
    }
}
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
