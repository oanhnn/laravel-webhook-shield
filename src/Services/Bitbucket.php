<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Bitbucket
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Bitbucket implements Service
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Bitbucket service constructor.
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
        $generated = hash_hmac('sha256', $request->getContent(), $this->token);

        return hash_equals($generated, $request->header('X-Hub-Signature'));
    }
}
