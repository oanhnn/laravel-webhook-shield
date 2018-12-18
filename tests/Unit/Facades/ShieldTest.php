<?php

namespace Laravel\WebhookShield\Tests\Unit\Facades;

use Laravel\WebhookShield\Facades\Shield;
use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class ShieldTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit\Facades
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class ShieldTest extends TestCase
{
    public function testGetFacadeAccessor()
    {
        $instance = Shield::getFacadeRoot();

        $this->assertInstanceOf(Manager::class, $instance);
    }
}
