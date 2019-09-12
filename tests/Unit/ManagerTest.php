<?php

namespace Laravel\WebhookShield\Tests\Unit;

use Closure;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Exceptions\UnknownServiceException;
use Laravel\WebhookShield\Exceptions\UnsupportedDriverException;
use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Services\Facebook;
use Laravel\WebhookShield\Services\Github;
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

    /**
     * Setting up before test
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('webhook-shield.services', [
            'github' => [
                'driver' => 'github',
                'options' => [
                    'token' => 'foo',
                ],
            ],
            'facebook' => [
                'driver' => Facebook::class,
                'options' => [
                    'secret' => 'bar',
                ],
            ],
            'custom' => [
                'driver' => 'baz',
                'options' => [
                    'token' => 'baz',
                ],
            ],
        ]);
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorWithoutService()
    {
        config()->set('webhook-shield.services', []);

        $manager = new Manager($this->app);

        $this->assertEquals([], $this->getNonPublicProperty($manager, 'customCreators'));
        $this->assertEquals([], $this->getNonPublicProperty($manager, 'services'));
        $this->assertEquals([], $manager->services());
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorWithService()
    {
        $manager = new Manager($this->app);

        $this->assertEquals([], $this->getNonPublicProperty($manager, 'customCreators'));
        $this->assertEquals([], $this->getNonPublicProperty($manager, 'services'));
        $this->assertEquals(['github', 'facebook', 'custom'], $manager->services());
    }

    /**
     * @throws \ReflectionException
     */
    public function testExtendDriver()
    {
        $manager = new Manager($this->app);
        $closure = function ($app, array $config = []) {
            //
        };

        $manager->extend('baz', $closure);

        $this->assertTrue(is_array($this->getNonPublicProperty($manager, 'customCreators')));
        $this->assertArrayHasKey('baz', $this->getNonPublicProperty($manager, 'customCreators'));
        $this->assertSame($closure, $this->getNonPublicProperty($manager, 'customCreators')['baz']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCallCustomCreator()
    {
        $config = [];
        $manager = new Manager($this->app);
        $closure = $this->mockClosure($config);

        $manager->extend('baz', $closure);

        $this->invokeNonPublicMethod($manager, 'callCustomCreator', 'baz', $config);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateServiceWithDriverName()
    {
        $manager = new Manager($this->app);
        $service = $this->invokeNonPublicMethod($manager, 'createService', 'github', ['token' => 'foo']);

        $this->assertInstanceOf(Service::class, $service);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateServiceWithDriverClass()
    {
        $manager = new Manager($this->app);
        $service = $this->invokeNonPublicMethod(
            $manager,
            'createService',
            Facebook::class,
            ['secret' => 'bar']
        );

        $this->assertInstanceOf(Service::class, $service);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateServiceWithCustomDriver()
    {
        $config = [];
        $manager = new Manager($this->app);
        $closure = $this->mockClosure($config);

        $manager->extend('baz', $closure);

        $this->invokeNonPublicMethod($manager, 'createService', 'baz', $config);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateServiceFailure()
    {
        $manager = new Manager($this->app);

        $this->expectException(UnsupportedDriverException::class);
        $this->expectExceptionMessage('Driver [baz] is unsupported.');

        $this->invokeNonPublicMethod($manager, 'createService', 'baz', []);
    }

    /**
     *
     */
    public function testServiceWasCreatedOnlyOneTime()
    {
        $mockObj = $this->createPartialMock(\stdClass::class, ['__invoke']);

        $mockObj->expects($this->once())
            ->method('__invoke')
            ->with($this->app, ['token' => 'baz'])
            ->willReturn(new Github(['token' => 'baz']));

        $manager = new Manager($this->app);
        $closure = Closure::fromCallable($mockObj);

        $manager->extend('baz', $closure);

        $manager->service('custom');
        $manager->service('custom');
    }

    /**
     *
     */
    public function testServiceWithoutConfig()
    {
        $manager = new Manager($this->app);

        $this->expectException(UnknownServiceException::class);
        $this->expectExceptionMessage('Service [trello] not defined.');

        $manager->service('trello');
    }

    /**
     *
     */
    public function testServiceWithoutDriver()
    {
        config()->set('webhook-shield.services', [
            'github' => [
                'options' => [
                    'token' => 'foo',
                ],
            ],
        ]);

        $manager = new Manager($this->app);

        $this->expectException(UnknownServiceException::class);
        $this->expectExceptionMessage('Service [github] must have a driver.');

        $manager->service('github');
    }

    /**
     *
     */
    public function testServiceWithUnsupportedDriver()
    {
        $manager = new Manager($this->app);

        $this->expectException(UnsupportedDriverException::class);
        $this->expectExceptionMessage('Driver [baz] is unsupported.');

        $manager->service('custom');
    }

    /**
     *
     */
    public function testServiceWithInvalidDriver()
    {
        $manager = new Manager($this->app);
        $closure = $this->mockClosure(['token' => 'baz']);

        $manager->extend('baz', $closure);

        $this->expectException(UnsupportedDriverException::class);
        $this->expectExceptionMessage('Driver [baz] must implement [Laravel\\WebhookShield\\Contracts\\Service].');

        $manager->service('custom');
    }

    /**
     * @param array $config
     * @return Closure
     */
    protected function mockClosure(array $config): Closure
    {
        $mockObj = $this->createPartialMock(\stdClass::class, ['__invoke']);

        $mockObj->expects($this->once())
            ->method('__invoke')
            ->with($this->app, $config)
            ->willReturn(true);

        return Closure::fromCallable($mockObj);
    }
}
