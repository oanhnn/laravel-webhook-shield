<?php

namespace Laravel\WebhookShield\Tests\Unit\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Services\Gitlab;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class GitlabTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class GitlabTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Gitlab
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new Gitlab([
            'token' => 'foo',
        ]);
    }

    /**
     * Tear down after test
     */
    protected function tearDown()
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
            'X-Gitlab-Token' => $token,
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
        $this->assertEquals(['X-Gitlab-Token'], $this->service->headerKeys());
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
