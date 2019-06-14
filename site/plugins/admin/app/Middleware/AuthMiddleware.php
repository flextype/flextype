<?php

namespace Flextype;

use Flextype\Component\Session\Session;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (Session::exists('role') && Session::get('role') == 'admin') {
            $response = $next($request, $response);
        } else {
            $response = $response->withRedirect($this->router->pathFor('admin.users.login'));
        }

        return $response;
    }
}
