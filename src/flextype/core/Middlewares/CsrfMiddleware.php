<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CsrfMiddleware
{
    /**
     * __invoke
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     * @param callable $next     Next middleware
     */
    /*public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $data = $request->getParsedBody();

        if (isset($data[flextype('csrf')->getTokenName()])) {
            if (flextype('csrf')->isValid($data[flextype('csrf')->getTokenName()])) {
                $response = $next($request, $response);
            } else {
                die('This looks like a cross-site request forgery!');
            }
        } else {
            $response = $next($request, $response);
        }

        return $response;
    }*/


    /**
     * Invoke
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $data = $request->getParsedBody();
    
        if (isset($data[csrf()->getTokenName()])) {
            if (csrf()->isValid($data[csrf()->getTokenName()])) {
                return $response;
            } else {
                $response = new Response();
                $response->getBody()->write('This looks like a cross-site request forgery!');
                return $response;
            }
        }

        return $response;
    }
}
