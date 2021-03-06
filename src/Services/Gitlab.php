<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Gitlab
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Gitlab implements Service
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Gitlab service constructor.
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
        return ['X-Gitlab-Token'];
    }

    /**
     * Verify request
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        return hash_equals($this->token, $request->header('X-Gitlab-Token'));
    }
}
