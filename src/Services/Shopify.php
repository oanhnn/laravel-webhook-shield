<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Shopify
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Shopify implements Service
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Shopify service constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->token = Arr::get($config, 'token');
    }

    /**
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array
    {
        return ['X-Shopify-Hmac-SHA256'];
    }

    /**
     * Verify request
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        $generated = base64_encode(hash_hmac('sha256', $request->getContent(), $this->token, true));

        return hash_equals($generated, $request->header('X-Shopify-Hmac-SHA256'));
    }
}
