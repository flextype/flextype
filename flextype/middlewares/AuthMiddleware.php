<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @property Router $router
 */
class AuthMiddleware extends Middleware
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
        if (Session::exists('role') && Session::get('role') === 'admin') {
            $response = $next($request, $response);
        } else {
            $response = $response->withRedirect($this->router->pathFor('admin.users.login'));
        }

        return $response;
    }
}
