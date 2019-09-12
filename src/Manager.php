<?php

namespace Laravel\WebhookShield;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Exceptions\UnknownServiceException;
use Laravel\WebhookShield\Exceptions\UnsupportedDriverException;

/**
 * Class Manager
 *
 * @package     Laravel\WebhookShield
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Manager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * The registered custom service creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created "service".
     *
     * @var array
     */
    protected $services = [];

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Container\Container $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a service instance.
     *
     * @param  string $name
     * @return Service
     * @throws UnknownServiceException
     * @throws UnsupportedDriverException
     */
    public function service(string $name): Service
    {
        if (!isset($this->services[$name])) {
            if (!Config::has("webhook-shield.services.{$name}")) {
                throw new UnknownServiceException("Service [{$name}] not defined.");
            }
            if (!Config::has("webhook-shield.services.{$name}.driver")) {
                throw new UnknownServiceException("Service [{$name}] must have a driver.");
            }

            $driver = Config::get("webhook-shield.services.{$name}.driver");
            $config = Config::get("webhook-shield.services.{$name}.options", []);

            $this->services[$name] = $this->createService($driver, $config);

            if (!$this->services[$name] instanceof Service) {
                unset($this->services[$name]);
                $interface = Service::class;

                throw new UnsupportedDriverException("Driver [{$driver}] must implement [{$interface}].");
            }
        }

        return $this->services[$name];
    }

    /**
     * Create a new service instance for the given service.
     *
     * We will check to see if a custom creator method exists for the given driver,
     * and will call the Closure if so, which allows us to have a more generic
     * resolver for the drivers themselves which applies to all drivers.
     *
     * @param  string $driver
     * @param  array $config
     * @return mixed
     * @throws UnsupportedDriverException
     */
    protected function createService($driver, array $config = [])
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver, $config);
        }

        $class = class_exists($driver) ? $driver : __NAMESPACE__ . '\\Services\\' . Str::studly($driver);
        if (class_exists($class)) {
            return new $class($config);
        }

        throw new UnsupportedDriverException("Driver [{$driver}] is unsupported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string $driver
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator($driver, array $config = [])
    {
        return $this->customCreators[$driver]($this->app, $config);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string   $driver
     * @param  \Closure $callback
     * @return self
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * @return array
     */
    public function services()
    {
        return array_keys(Config::get('webhook-shield.services'));
    }
}
