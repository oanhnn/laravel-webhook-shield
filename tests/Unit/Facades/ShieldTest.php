<?php

namespace Laravel\WebhookShield\Tests\Unit\Facades;

use Laravel\WebhookShield\Facades\Shield;
use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Tests\TestCase;

class ShieldTest extends TestCase
{
    public function testGetFacadeAccessor()
    {
        $instance = Shield::getFacadeRoot();

        $this->assertInstanceOf(Manager::class, $instance);
    }
}
