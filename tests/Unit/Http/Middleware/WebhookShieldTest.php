<?php

namespace Laravel\WebhookShield\Tests\Unit\Http\Middleware;

use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

class WebhookShieldTest extends TestCase
{
    use NonPublicAccessibleTrait;

    public function testPasses()
    {
        $this->assertTrue(true);
    }
}
