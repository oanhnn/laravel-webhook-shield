<?php

namespace Laravel\WebhookShield\Tests\Unit\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;
use Laravel\WebhookShield\Services\Mailgun;
use Laravel\WebhookShield\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookShield\Tests\TestCase;

/**
 * Class MailgunTest
 *
 * @package     Laravel\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class MailgunTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Mailgun
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new Mailgun([
            'token' => 'foo',
            'tolerance' => 360,
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
     * @param  string $method
     * @param  array  $data
     * @param  string $token
     * @return Request
     */
    protected function createTestRequest(string $method, array $data, string $token)
    {
        $data['signature'] = hash_hmac('sha256', $data['timestamp'] . $data['token'], $token);

        return Request::create(
            'https://example.com',
            $method,
            $data
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $this->assertEquals('foo', $this->getNonPublicProperty($this->service, 'token'));
        $this->assertEquals(360, $this->getNonPublicProperty($this->service, 'tolerance'));
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
        $this->assertEquals([], $this->service->headerKeys());
    }

    /**
     * @param string $method
     * @param array  $data
     * @param string $token
     * @param bool   $excepted
     * @dataProvider dataTestVerify
     */
    public function testVerify(string $method, array $data, string $token, bool $excepted)
    {
        $request = $this->createTestRequest($method, $data, $token);

        $this->assertSame($excepted, $this->service->verify($request));
    }

    /**
     * @return array
     */
    public function dataTestVerify()
    {
        return [
            ['POST', ['token' => 'baz', 'timestamp' => time()], 'foo', true],
            ['GET', ['token' => 'baz', 'timestamp' => time()], 'foo', false],
            ['GET', ['token' => 'baz', 'timestamp' => strtotime('10 minutes ago')], 'foo', false],
            ['POST', ['token' => 'baz', 'timestamp' => time()], 'bar', false],
        ];
    }
}
