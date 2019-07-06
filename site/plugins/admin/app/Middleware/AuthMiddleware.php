<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next) : Response
    {
        if (Session::exists('role') && Session::get('role') == 'admin') {
            $response = $next($request, $response);
        } else {
            $response = $response->withRedirect($this->router->pathFor('admin.users.login'));
        }

        return $response;
    }
}
