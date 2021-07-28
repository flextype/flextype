<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Foundation\Flextype;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Psr7\Factory\StreamFactory;
use Atomastic\Csrf\Csrf;
use Atomastic\Registry\Registry;
use Atomastic\Session\Session;
use Cocur\Slugify\Slugify;
use DateTimeZone;

/**
 * Get Flextype Instance
 */
$flextype = flextype();


/**
 * Set Flextype base path
 */
flextype()->app()->setBasePath('/flextype');

flextype()->app()->add(new RoutingMiddleware(flextype()->app()->getRouteResolver(), flextype()->app()->getRouteCollector()->getRouteParser()));
flextype()->app()->add(new ContentLengthMiddleware());
flextype()->app()->add(new OutputBufferingMiddleware(new StreamFactory(), OutputBufferingMiddleware::APPEND));

/**
 * Init Registry
 */
flextype()->container()->set('registry', registry());

/**
 * Init Actions
 */
flextype()->container()->set('actions', actions());

/**
 * Preflight the Flextype
 */
include_once ROOT_DIR . '/src/flextype/preflight.php';


flextype()->app()->get('/hello/{name}', function ($name, Request $request, Response $response) {
    $response->getBody()->write('Hello' . $name);
    return $response;
})->setName('root');

flextype()->app()->getRouteCollector()->setCacheFile(PATH['tmp'] . '/routes.php');

flextype()->app()->addErrorMiddleware(true, true, true);

// Run flextype
flextype()->app()->run();