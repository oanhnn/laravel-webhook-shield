<?php

namespace Laravel\WebhookShield\Tests\Unit\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Services\Github;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class GithubTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class GithubTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Github
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new Github([
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
            'X-Hub-Signature' => 'sha1=' . hash_hmac('sha1', $content, $secret),
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
        $this->assertEquals(['X-Hub-Signature'], $this->service->headerKeys());
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
