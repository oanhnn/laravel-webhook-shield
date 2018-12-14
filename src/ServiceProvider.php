<?php

namespace Laravel\WebhookShield;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Laravel\WebhookShield\Http\Middleware\WebhookShield;

/**
 * Class ServiceProvider
 *
 * @package     Laravel\WebhookShield
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // register config
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/webhook-shield.php', 'webhook-shield');

        // register service
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager($app);
        });

        $this->app['router']->aliasMiddleware('shield', WebhookShield::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish vendor resources
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config/webhook-shield.php' => config_path('webhook-shield.php')
            ], 'config');
        }
    }
}
