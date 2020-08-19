<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Slim\Http\Environment;
use Slim\Http\Uri;

if ($flextype->container('registry')->get('flextype.settings.shortcode.shortcodes.url.enabled')) {
    // Shortcode: [url]
    $flextype->container('shortcode')->addHandler('url', static function () use ($flextype) {
        if ($flextype->container('registry')->has('flextype.settings.url') && $flextype->container('registry')->get('flextype.settings.url') !== '') {
            return $flextype->container('registry')->get('flextype.settings.url');
        }

        return Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
    });
}
