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

use function parsers;
use function registry;

// Shortcode: registry
// Usage: (registry get:flextype.manifest.version)
parsers()->shortcodes()->addHandler('registry', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled')) {
        return '';
    }

    if ($s->getParameter('get') != null) {

        $value   = parsers()->shortcodes()->parse($s->getParameter('get'));
        $default = ($s->getParameter('default') != null) ? parsers()->shortcodes()->parse($s->getParameter('default')) : null;
        $result  = registry()->get($value, $default);

        return is_array($result) ? serializers()->json()->encode($result) : $result;
    }

    return '';
});
