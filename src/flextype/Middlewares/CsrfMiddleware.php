<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CsrfMiddleware
{
    /**
     * __invoke
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     * @param callable $next     Next middleware
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $post_data = $request->getParsedBody();

        if (isset($post_data[flextype('csrf')->getTokenName()])) {
            if (flextype('csrf')->isValid($post_data[flextype('csrf')->getTokenName()])) {
                $response = $next($request, $response);
            } else {
                die('This looks like a cross-site request forgery!');
            }
        } else {
            $response = $next($request, $response);
        }

        return $response;
    }
}
