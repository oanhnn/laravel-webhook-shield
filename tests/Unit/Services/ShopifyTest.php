<?php

namespace Laravel\WebhookShield\Tests\Unit\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Services\Shopify;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class ShopifyTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class ShopifyTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Shopify
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new Shopify([
            'token' => 'foo',
        ]);
    }

    /**
     * Tear down after test
     */
    protected function tearDown(): void
    {
        unset($this->service);

        parent::tearDown();
    }

    /**
     * @param  string $content
     * @param  string $token
     * @return Request
     */
    protected function createTestRequest(string $content, string $token)
    {
        $request = Request::create(
            'https://example.com',
            'POST',
            [],
            [],
            [],
            [],
            $content
        );

        $request->headers->add([
            'X-Shopify-Hmac-SHA256' => base64_encode(hash_hmac('sha256', $content, $token, true)),
        ]);

        return $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $this->assertEquals('foo', $this->getNonPublicProperty($this->service, 'token'));
    }

    /**
     *
     */
    public function testIsService()
    {
        $this->assertInstanceOf(Service::class, $this->service);
    }

    /**
     *
     */
    public function testHeadersFields()
    {
        $this->assertEquals(['X-Shopify-Hmac-SHA256'], $this->service->headerKeys());
    }

    /**
     *
     */
    public function testVerifyValidRequest()
    {
        $request = $this->createTestRequest('test-content', 'foo');

        $this->assertTrue($this->service->verify($request));
    }

    /**
     *
     */
    public function testVerifyInvalidRequest()
    {
        $request = $this->createTestRequest('test-content', 'bar');

        $this->assertFalse($this->service->verify($request));
    }
}
