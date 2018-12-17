<?php

namespace Laravel\WebhookShield\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Http\Middleware\WebhookShield;
use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

class WebhookShieldTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @throws \ReflectionException
     */
    public function testPassesSuccess()
    {
        list($request, $manager) = $this->mockService([], true);

        $middleware = new WebhookShield($manager);

        $this->assertTrue($this->invokeNonPublicMethod($middleware, 'passes', 'foo', $request));
    }

    /**
     * @throws \ReflectionException
     */
    public function testPassesWhenMissingHeaders()
    {
        list($request, $manager) = $this->mockService(['bar'], true);

        $middleware = new WebhookShield($manager);

        $this->assertFalse($this->invokeNonPublicMethod($middleware, 'passes', 'foo', $request));
    }

    /**
     * @throws \ReflectionException
     */
    public function testPassesWhenFailingVerified()
    {
        list($request, $manager) = $this->mockService([], false);

        $middleware = new WebhookShield($manager);

        $this->assertFalse($this->invokeNonPublicMethod($middleware, 'passes', 'foo', $request));
    }

    /**
     * Test handle a request, that did passed
     */
    public function testHandlePassedRequest()
    {
        list($request, $manager) = $this->mockService([], true);

        $call = 0;
        $response = new Response();
        $next = function (Request $request) use (&$call, $response) {
            $call++;
            return $response;
        };

        $middleware = new WebhookShield($manager);

        $result = $middleware->handle($request, $next, 'foo');

        $this->assertSame($response, $result);
        $this->assertEquals(1, $call); // `next` closure was executed
    }

    /**
     * Test handle a request, that didn't passed
     */
    public function testHandleDontPassedRequest()
    {
        list($request, $manager) = $this->mockService([], false);

        $call = 0;
        $response = new Response();
        $next = function (Request $request) use (&$call, $response) {
            $call++;
            return $response;
        };

        $middleware = new WebhookShield($manager);

        $result = $middleware->handle($request, $next, 'foo');

        $this->assertNotSame($response, $result);
        $this->assertEquals(0, $call); // `next` closure wasn't executed

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        $this->assertEquals(Response::$statusTexts[Response::HTTP_BAD_REQUEST], $result->getContent());
    }

    /**
     * @param  array $headers
     * @param  bool  $verify
     * @return array
     */
    protected function mockService(array $headers, bool $verify)
    {
        $request = new Request();
        $request->headers->remove('bar');

        $service = $this->createMock(Service::class);
        $service->method('headerKeys')->willReturn($headers);
        $service->method('verify')->with($request)->willReturn($verify);

        $manager = $this->createMock(Manager::class);
        $manager->method('service')->with('foo')->willReturn($service);

        return [$request, $manager];
    }
}
