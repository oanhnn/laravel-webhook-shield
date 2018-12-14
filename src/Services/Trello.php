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
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array
    {
        // TODO: Implement headerKeys() method.
    }

    /**
     * Verify request
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        // TODO: Implement verify() method.
    }
}
