<?php

namespace Laravel\WebhookShield\Tests\Unit;

use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

class ManagerTest extends TestCase
{
    use NonPublicAccessibleTrait;

    public function testServices()
    {
        config()->set('webhook-shield.services', [
            'github' => [
                'driver' => 'github',
                'options' => [
                    'token' => 'abc',
                ],
            ],
        ]);

        $manager = new Manager($this->app);

        $this->assertEquals(['github'], $manager->services());
    }
}
