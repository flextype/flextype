<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Middlewares\ResponseTime;
use Middlewares\TrailingSlash;

/**
 * Add middleware CSRF (cross-site request forgery) protection for all routes
 */
$app->add($flextype->get('csrf'));

/**
 * Add middleware TrailingSlash for all routes
 */
$app->add((new TrailingSlash(false))->redirect(true));

/**
 * Add middleware ResponseTime for all routes
 */
$app->add((new ResponseTime()));
