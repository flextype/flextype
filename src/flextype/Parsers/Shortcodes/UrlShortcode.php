<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use Slim\Http\Environment;
use Slim\Http\Uri;

// Shortcode: [url]
if (registry()->get('flextype.settings.parsers.shortcodes.url.enabled')) {
    parsers()->shortcodes()->addHandler('url', static function () {
        if (registry()->has('flextype.settings.url') && registry()->get('flextype.settings.url') !== '') {
            return registry()->get('flextype.settings.url');
        }

        return app()->getBasePath();
    });
}
