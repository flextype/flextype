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

// Shortcode: filesystem
// Usage: (filesystem get file:'1.txt)
parsers()->shortcodes()->addHandler('filesystem', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.filesystem.enabled')) {
        return '';
    }

    $params = $s->getParameters();

    if (collection(array_keys($params))->filter(fn ($v) => $v == 'get')->count() > 0 && 
        isset($params['file']) && 
        registry()->get('flextype.settings.parsers.shortcodes.shortcodes.filesystem.get.enabled') === true) {

        $file = parsers()->shortcodes()->parse($params['file']);

        return filesystem()->file($file)->exists() ? filesystem()->file($file)->get() : '';
    }

    return '';
});