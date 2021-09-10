<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Flextype\Whoops;

class WhoopsMiddleware implements MiddlewareInterface 
{

    protected $settings = [];
    protected $handlers = [];

    /**
     * Instance the whoops middleware object
     *
     * @param array $settings
     * @param array $handlers
     */
    public function __construct(array $settings = [], array $handlers = []) 
    {
        $this->settings = $settings;
        $this->handlers = $handlers;
    }

    /**
     * Handle the requests
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface 
    {
        $whoopsGuard = new Whoops($this->settings);
        $whoopsGuard->setRequest($request);
        $whoopsGuard->setHandlers($this->handlers);
        $whoopsGuard->install();

        return $handler->handle($request);
    }

}