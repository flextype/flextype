<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Tuupola\Middleware\CorsMiddleware;

/**
 * Add middleware CSRF (cross-site request forgery) protection for all routes
 */
$app->add($flextype->get('csrf'));

/**
 * Add middleware CorsMiddleware for Cross-origin resource sharing.
 */
$app->add(new CorsMiddleware);
