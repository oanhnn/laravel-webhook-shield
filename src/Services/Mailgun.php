<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Mailgun
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Mailgun implements Service
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $tolerance;

    /**
     * Mailgun constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->token = Arr::get($config, 'token');
        $this->tolerance = Arr::get($config, 'tolerance', 5 * 60); // default tolerance is 5 minutes
    }

    /**
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array
    {
        return [];
    }

    /**
     * Verify request
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        $timestamp = $request->input('timestamp');

        if (!$request->isMethod('POST') || abs(time() - $timestamp) > $this->tolerance) {
            return false;
        }

        $generated = hash_hmac('sha256', $timestamp . $request->input('token'), $this->token);

        return $generated === $request->input('signature');
    }
}
