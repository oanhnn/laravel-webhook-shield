<?php

namespace Laravel\WebhookShield\Tests\Unit\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Services\Trello;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class TrelloTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class TrelloTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Trello
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new Trello([
            'secret' => 'foo',
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
     * @param  string $secret
     * @return Request
     */
    protected function createTestRequest(string $content, string $secret)
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
            'X-Trello-Webhook' => base64_encode(hash_hmac('sha1', trim($content) . $request->fullUrl(), $secret, true)),
        ]);

        return $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $this->assertEquals('foo', $this->getNonPublicProperty($this->service, 'secret'));
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
        $this->assertEquals(['X-Trello-Webhook'], $this->service->headerKeys());
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
