<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Facebook
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Facebook implements Service
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * Facebook service constructor.
     *
     * @param  array $config
     */
    public function __construct(array $config)
    {
        $this->secret = $config['secret'] ?? '';
    }

    /**
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array
    {
        return ['X-Hub-Signature'];
    }

    /**
     * Verify request
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        $generated = 'sha1=' . hash_hmac('sha1', $request->getContent(), $this->secret);

        return hash_equals($generated, $request->header('X-Hub-Signature'));
    }
}
