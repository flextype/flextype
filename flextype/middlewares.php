<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Maintained by Sergey Romanenko and Flextype Community.
 *
 * @license https://github.com/flextype/flextype/blob/master/LICENSE.txt (MIT License)
 */

namespace Flextype;

use Psr7Middlewares\Middleware\ResponseTime;
use Psr7Middlewares\Middleware\TrailingSlash;

/**
 * Add middleware CSRF (cross-site request forgery) protection for all routes
 */
$app->add($flextype->get('csrf'));

/**
 * Add middleware TrailingSlash for all routes
 */
$app->add((new TrailingSlash(false))->redirect(301));

/**
 * Add middleware ResponseTime for all routes
 */
$app->add((new ResponseTime()));
