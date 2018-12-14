<?php

namespace Laravel\WebhookShield\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\WebhookShield\Manager;

/**
 * Class WebhookShield
 *
 * @package     Laravel\WebhookShield\Http\Middleware
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class WebhookShield
{
    /**
     * @var \Laravel\WebhookShield\Manager
     */
    protected $manager;

    /**
     * Constructor
     *
     * @param  \Laravel\WebhookShield\Manager $manager
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string                   $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, string $service)
    {
        if ($this->passes($service, $request)) {
            return $next($request);
        }

        return Response::create(Response::$statusTexts[Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param  string                   $service
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function passes(string $service, Request $request): bool
    {
        $shield = $this->manager->service($service);

        foreach ($shield->headerKeys() as $header) {
            if (!$request->hasHeader($header)) {
                return false;
            }
        }

        return $shield->verify($request);
    }
}
