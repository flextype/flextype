<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

use function Flextype\getAbsoluteUrl;
use function Flextype\getBasePath;
use function Flextype\getBaseUrl;
use function Flextype\getProjectUrl;
use function Flextype\getUriString;
use function Flextype\parsers;
use function Flextype\registry;
use function Flextype\serializers;
use function Flextype\urlFor;

// Shortcode: getBaseUrl
// Usage: (getBaseUrl)
parsers()->shortcodes()->addHandler('getBaseUrl', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getBaseUrl();
});

// Shortcode: getBasePath
// Usage: (getBasePath)
parsers()->shortcodes()->addHandler('getBasePath', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getBasePath();
});

// Shortcode: getAbsoluteUrl
// Usage: (getAbsoluteUrl)
parsers()->shortcodes()->addHandler('getAbsoluteUrl', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getAbsoluteUrl();
});

// Shortcode: getProjectUrl
// Usage: (getProjectUrl)
parsers()->shortcodes()->addHandler('getProjectUrl', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getProjectUrl();
});

// Shortcode: getUriString
// Usage: (getUriString)
parsers()->shortcodes()->addHandler('getUriString', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getUriString();
});

// Shortcode: urlFor
// Usage: (urlFor routeName='route-name' data='{"foo": "Foo"}' queryParams='{"foo": "Foo"}')
parsers()->shortcodes()->addHandler('urlFor', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return urlFor(
        $s->getParameter('routeName'),
        $s->getParameter('data') !== null ? serializers()->json()->decode(parsers()->shortcodes()->parse($s->getParameter('data'))) : [],
        $s->getParameter('queryParams') !== null ? serializers()->json()->decode(parsers()->shortcodes()->parse($s->getParameter('queryParams'))) : [],
    );
});
