<?php

namespace Laravel\WebhookShield\Tests\Unit;

use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class ManagerTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
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
