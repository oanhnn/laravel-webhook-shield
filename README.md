# Introduction

[![Latest Version](https://img.shields.io/packagist/v/oanhnn/laravel-webhook-shield.svg)](https://packagist.org/packages/oanhnn/laravel-webhook-shield)
[![Software License](https://img.shields.io/github/license/oanhnn/laravel-webhook-shield.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/oanhnn/laravel-webhook-shield/master.svg)](https://travis-ci.org/oanhnn/laravel-webhook-shield)
[![Coverage Status](https://img.shields.io/coveralls/github/oanhnn/laravel-webhook-shield/master.svg)](https://coveralls.io/github/oanhnn/laravel-webhook-shield?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/oanhnn/laravel-webhook-shield.svg)](https://packagist.org/packages/oanhnn/laravel-webhook-shield)
[![Requires PHP](https://img.shields.io/travis/php-v/oanhnn/laravel-webhook-shield.svg)](https://travis-ci.org/oanhnn/laravel-webhook-shield)

Protects against unverified webhooks from 3rd party services on Laravel 5.5+

## Features

* Supported Laravel 5.5+
* Allow multi services with same driver (Useful for two webhook endpoints of two facebook applications)
* Supported drivers
   - [x] Bitbucket
   - [x] Facebook
   - [x] Github
   - [x] Gitlab
   - [x] Mailgun
   - [x] Shopify
   - [x] Trello
* Easy to extend more drivers

## Requirements

* php >=7.1.3
* Laravel 5.5+

## Installation

Begin by pulling in the package through Composer.

```bash
$ composer require oanhnn/laravel-webhook-shield
```

The package will automatically register itself.   
You can publish the config-file with:

```bash
$ php artisan vendor:publish --provider=Laravel\WebhookShield\ServiceProvider
```

## Usage

### Configure services

In configuration file, you can define services

```php
<?php

return [
    'services' => [
        'github' => [
            'driver' => 'github',
            'options' => [],
        ],
        'facebook' => [
            'driver' => \Laravel\WebhookShield\Services\Facebook::class,
            'options' => [],
        ],
        'custom' => [
            'driver' => 'custom-driver',
            'options' => [],
        ],
    ],
];
```

### Protects webhook routes

```php
Route::middleware('shield:facebook')->post('/webhook/facebook', 'WebhookController@facebook');
Route::middleware('shield:github')->post('/webhook/facebook', 'WebhookController@github');
Route::middleware('shield:custom')->post('/webhook/custom', 'WebhookController@custom');
```

### Make custom driver

Make a service implement class

```php
<?php
namespace App\Services;

use Laravel\WebhookShield\Contracts\Service;

class CustomService implements Service
{
    // ...
}
```

And register this driver in app/Providers/AppServiceProvider.php

```php
<?php
namespace App\Providers;

use App\Services\CustomService;
use Illuminate\Support\ServiceProvider;
use Laravel\WebhookShield\Facades\Shield;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Shield::extend('custom-driver', function ($app, $config) {
            return new CustomService($config);
        });
        // ...
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ...
    }
}
```

## Changelog

See all change logs in [CHANGELOG](CHANGELOG.md)

## Testing

```bash
$ git clone git@github.com/oanhnn/laravel-webhook-shield.git /path
$ cd /path
$ composer install
$ composer phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email to [Oanh Nguyen](mailto:oanhnn.bk@gmail.com) instead of 
using the issue tracker.

## Credits

- [Oanh Nguyen](https://github.com/oanhnn)
- [All Contributors](../../contributors)

## License

This project is released under the MIT License.   
Copyright © 2018 [Oanh Nguyen](https://oanhnn.github.io/).
