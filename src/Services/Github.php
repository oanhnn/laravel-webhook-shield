<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Github
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Github implements Service
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Github service constructor.
     *
     * @param  array $config
     */
    public function __construct(array $config)
    {
        $this->token = $config['token'] ?? '';
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
        $generated = 'sha1=' . hash_hmac('sha1', $request->getContent(), $this->token);

        return hash_equals($generated, $request->header('X-Hub-Signature'));
    }
}
