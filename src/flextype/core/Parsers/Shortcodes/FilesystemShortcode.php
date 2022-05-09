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

// Shortcode: [filesystem]
parsers()->shortcodes()->addHandler('filesystem', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.filesystem.enabled')) {
        return '';
    }

    $varsDelimeter = $s->getParameter('varsDelimeter') ?: '|';

    if ($s->getParameter('get') != null && registry()->get('flextype.settings.parsers.shortcodes.shortcodes.filesystem.get.enabled') === true) {

        // Get vars
        foreach($s->getParameters() as $key => $value) {
            $vars = $value !== null ? strings($value)->contains($varsDelimeter) ? explode($varsDelimeter, $value) : [$value] : [];
        }

        return filesystem()->file($vars[0])->exists() ? filesystem()->file($vars[0])->get() : '';
    }

    return '';
});