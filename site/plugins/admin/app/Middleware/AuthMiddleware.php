<?php

namespace Flextype;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        die('auth');
    }
}
