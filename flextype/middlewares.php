<?php

namespace Flextype;

use Psr7Middlewares\Middleware;
use Psr7Middlewares\Middleware\TrailingSlash;

/**
 * Add middleware CSRF (cross-site request forgery) protection for all routes
 */
$app->add($flextype->get('csrf'));

/**
 * Add middleware TrailingSlash for all routes
 */
$app->add((new TrailingSlash(false))->redirect(301));
