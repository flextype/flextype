<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Slim\Http\Environment;
use Slim\Http\Uri;

// Shortcode: [base_url]
$flextype['shortcodes']->addHandler('base_url', static function () {
    return Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
});
