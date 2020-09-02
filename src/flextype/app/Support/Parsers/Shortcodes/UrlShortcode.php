<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Slim\Http\Environment;
use Slim\Http\Uri;

// Shortcode: [url]
if (flextype('registry')->get('flextype.settings.shortcode.shortcodes.url.enabled')) {
    flextype('shortcode')->addHandler('url', static function () {
        if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
            return flextype('registry')->get('flextype.settings.url');
        }

        return Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
    });
}
