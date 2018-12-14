<?php

namespace Laravel\WebhookShield\Contracts;

use Illuminate\Http\Request;

/**
 * Interface Service
 *
 * @package     Laravel\WebhookShield\Contracts
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
interface Service
{
    /**
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array;

    /**
     * Verify request
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verify(Request $request): bool;
}
