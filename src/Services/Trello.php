<?php

namespace Laravel\WebhookShield\Services;

use Illuminate\Http\Request;
use Laravel\WebhookShield\Contracts\Service;

/**
 * Class Trello
 *
 * @package     Laravel\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Trello implements Service
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
        return ['X-Trello-Webhook'];
    }

    /**
     * Verify request
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        $content = trim($request->getContent()) . $request->fullUrl();
        $generated = base64_encode(hash_hmac('sha1', $content, $this->secret, true));

        return hash_equals($generated, $request->header('X-Trello-Webhook'));
    }
}
