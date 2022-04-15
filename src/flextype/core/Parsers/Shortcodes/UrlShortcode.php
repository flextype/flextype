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
use function app;
use function parsers;
use function registry;

// Shortcode: [getBaseUrl]
parsers()->shortcodes()->addHandler('getBaseUrl', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getBaseUrl();
});

// Shortcode: [getBasePath]
parsers()->shortcodes()->addHandler('getBasePath', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return getBasePath();
});

// Shortcode: [getAbsoluteUrl]
parsers()->shortcodes()->addHandler('getAbsoluteUrl', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }
    
    return getAbsoluteUrl();
});

// Shortcode: [getUriString]
parsers()->shortcodes()->addHandler('getUriString', static function () {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }
    
    return getUriString();
});

// Shortcode: [urlFor routeName="route-name" data='{"foo": "Foo"}' queryParams='{"foo": "Foo"}']
parsers()->shortcodes()->addHandler('urlFor', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.url.enabled')) {
        return '';
    }

    return urlFor($s->getParameter('routeName'), 
                  $s->getParameter('data') != null ? serializers()->json()->decode($s->getParameter('data')) : [],
                  $s->getParameter('queryParams') != null ? serializers()->json()->decode($s->getParameter('queryParams')) : [],);
});