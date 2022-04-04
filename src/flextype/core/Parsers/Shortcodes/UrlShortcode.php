<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers\Shortcodes;

use function app;
use function parsers;
use function registry;

// Shortcode: [url]
parsers()->shortcodes()->addHandler('url', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    if (registry()->has('flextype.settings.url') && registry()->get('flextype.settings.url') !== '') {
        return registry()->get('flextype.settings.url');
    }

    return app()->getBasePath();
});
