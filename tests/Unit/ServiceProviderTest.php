<?php

namespace Laravel\WebhookShield\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Laravel\WebhookShield\Manager;
use Laravel\WebhookShield\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->files = new Filesystem();
    }

    /**
     * Clear up after test
     */
    protected function tearDown()
    {
        $this->files->delete([
            $this->app->configPath('webhook-shield.php'),
        ]);

        parent::tearDown();
    }

    /**
     * Test file webhook-shield.php is existed in config directory after run
     *
     * php artisan vendor:publish --provider="Laravel\\WebhookShield\\ServiceProvider" --tag=config
     */
    public function testPublishVendorConfig()
    {
        $sourceFile = dirname(dirname(__DIR__)) . '/config/webhook-shield.php';
        $targetFile = config_path('webhook-shield.php');

        $this->assertFileNotExists($targetFile);

        $this->artisan('vendor:publish', [
            '--provider' => 'Laravel\\WebhookShield\\ServiceProvider',
            '--tag' => 'config',
        ]);

        $this->assertFileExists($targetFile);
        $this->assertEquals(file_get_contents($sourceFile), file_get_contents($targetFile));
    }

    /**
     * Test default config: key `services` is an empty array
     */
    public function testDefaultConfigValues()
    {
        $this->assertIsArray(config('webhook-shield.services'));
        $this->assertEmpty(config('webhook-shield.services'));
    }

    /**
     * Test manager is bound in application container
     */
    public function testBoundManager()
    {
        $this->assertTrue($this->app->bound(Manager::class));
    }
}
