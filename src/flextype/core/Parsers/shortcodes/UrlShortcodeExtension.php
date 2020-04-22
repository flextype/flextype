<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Slim\Http\Environment;
use Slim\Http\Uri;

// Shortcode: [url]
$flextype['shortcodes']->addHandler('url', static function () use ($flextype) {
    if ($flextype['registry']->has('flextype.settings.url') && $flextype['registry']->get('flextype.settings.url') != '') {
        return $flextype['registry']->get('flextype.settings.url');
    } else {
        return Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
    }
});
