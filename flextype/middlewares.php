<?php

/**
 * @package Flextype
 *
 * @author Romanenko Sergey <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Psr7Middlewares\Middleware;
use Psr7Middlewares\Middleware\TrailingSlash;
use Psr7Middlewares\Middleware\ResponseTime;

/**
 * Add middleware CSRF (cross-site request forgery) protection for all routes
 */
$app->add($flextype->get('csrf'));

/**
 * Add middleware TrailingSlash for all routes
 */
$app->add((new TrailingSlash(false))->redirect(301));

$app->add((new ResponseTime()));
