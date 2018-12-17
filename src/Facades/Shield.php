<?php

namespace Laravel\WebhookShield\Facades;

use Illuminate\Support\Facades\Facade;
use Laravel\WebhookShield\Manager;

/**
 * Class Shield
 *
 * @package     Laravel\WebhookShield\Facades
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 *
 * @method static \Laravel\WebhookShield\Contracts\Service service(string $name)
 * @method static array services()
 * @method static \Laravel\WebhookShield\Manager extend(string $driver, \Closure $callback)
 */
class Shield extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
