<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use function Flextype\csrf;

class CsrfMiddleware
{
    /**
     * Invoke
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $data     = $request->getParsedBody();

        if (isset($data[csrf()->getTokenName()])) {
            if (csrf()->isValid($data[csrf()->getTokenName()])) {
                return $response;
            }

            $response = new Response();
            $response->getBody()->write('This looks like a cross-site request forgery!');

            return $response;
        }

        return $response;
    }
}
